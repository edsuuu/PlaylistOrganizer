<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) Config::get('services.google.gemini.key');
        $this->baseUrl = (string) Config::get('services.google.gemini.url');
    }

    /**
     * Verifica se as chaves de API necessárias estão presentes.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }

    /**
     * Gera recomendações de músicas (JSON format) baseado em um prompt.
     */
    public function generatePlaylistRecommendations(string $userPrompt, string $context = ''): array
    {
        if (!$this->isConfigured()) {
            Log::channel('spotify')->error("Gemini API Key não configurada.");
            return [];
        }

        // Construção robusta da URL para evitar duplicidade de ":generateContent"
        $url = rtrim($this->baseUrl, '/');
        if (!str_contains($url, ':generateContent')) {
            $url .= ':generateContent';
        }
        $url .= "?key={$this->apiKey}";

        $systemPrompt = "Você é um especialista em curadoria de playlists do Spotify. 
        INSTRUÇÃO CRÍTICA: Responda EXCLUSIVAMENTE com um array JSON válido. 
        Não inclua NENHUM texto fora do JSON. Não inclua markdown (```json). 
        Cada objeto deve ter as chaves 'name' e 'artist'. 
        Sugira exatamente 15 músicas que se encaixem perfeitamente no prompt.
        Se houver contexto de músicas curtidas, NÃO as inclua no resultado final (sugira algo novo de acordo com o gosto do usuário).";

        if ($context) {
            $systemPrompt .= "\nContexto de músicas do usuário (para inspiração e exclusão): {$context}";
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => "Prompt do Usuário: " . $userPrompt . "\nSistema: " . $systemPrompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.8,
                'maxOutputTokens' => 4096,
            ]
        ];

        try {
            Log::channel('spotify')->info("Chamando Gemini API", ['payload' => $payload]);

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(60)
                ->post($url, $payload);

            if ($response->failed()) {
                Log::channel('spotify')->error("Erro na API do Gemini: ", $response->json() ?? ['raw' => $response->body()]);
                return [];
            }

            $data = $response->json();
            Log::channel('spotify')->info("Resposta Gemini API", ['data' => $data]);

            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Limpa possíveis maracações de markdown (```json ... ```)
            $jsonStr = trim($text);
            if (str_starts_with($jsonStr, '```')) {
                $jsonStr = preg_replace('/^```(?:json)?|```$/', '', $jsonStr);
            }

            return json_decode(trim($jsonStr), true) ?? [];

        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao gerar recomendações Gemini: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca uma alternativa para uma track específica que o usuário não gostou.
     */
    public function getAlternativeTrack(string $originalPrompt, array $currentTracks): ?array
    {
        $currentStr = collect($currentTracks)->map(fn($t) => "{$t['name']} by {$t['artist']}")->implode(', ');
        
        $prompt = "Dê uma alternativa musical para o tema '{$originalPrompt}', 
        mas que não seja nenhuma destas: {$currentStr}. 
        Retorne APENAS um objeto JSON como {\"name\": \"...\", \"artist\": \"...\"}";

        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ];

        try {
            $url = rtrim($this->baseUrl, '/') . "?key={$this->apiKey}";
            if (!str_contains($url, ':generateContent')) {
                $url = str_replace("?key=", ":generateContent?key=", $url);
            }

            $response = Http::post($url, $payload);
            $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            $jsonStr = trim($text);
            if (str_starts_with($jsonStr, '```')) {
                $jsonStr = preg_replace('/^```(?:json)?|```$/', '', $jsonStr);
            }

            return json_decode(trim($jsonStr), true);
        } catch (\Exception $e) {
            return null;
        }
    }
}
