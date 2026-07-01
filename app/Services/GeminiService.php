<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;

    /** Host base da API, sem o trecho /models/{model}:generateContent. */
    private string $apiBase;

    /** Modelos a tentar em ordem: principal e depois o fallback. */
    private array $models;

    public function __construct()
    {
        $this->apiKey = (string) Config::get('services.google.gemini.key');

        // Deriva o host base a partir da URL configurada, ignorando o modelo embutido nela.
        $url = rtrim((string) Config::get('services.google.gemini.url'), '/');
        $base = preg_replace('#/models/.*$#', '', $url);
        $this->apiBase = $base !== '' ? $base : 'https://generativelanguage.googleapis.com/v1beta';

        // Modelo principal + fallback (configuráveis via .env).
        $this->models = array_values(array_unique(array_filter([
            (string) Config::get('services.google.gemini.model'),
            (string) Config::get('services.google.gemini.fallback_model'),
        ])));
    }

    /**
     * Verifica se a chave de API e ao menos um modelo estão configurados.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey) && ! empty($this->apiBase) && ! empty($this->models);
    }

    /**
     * Monta o endpoint de geração para um modelo específico.
     */
    private function endpoint(string $model): string
    {
        return "{$this->apiBase}/models/{$model}:generateContent?key={$this->apiKey}";
    }

    /**
     * Gera recomendações de músicas (JSON format) baseado em um prompt.
     */
    public function generatePlaylistRecommendations(string $userPrompt, string $context = ''): array
    {
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
                        ['text' => 'Prompt do Usuário: '.$userPrompt."\nSistema: ".$systemPrompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.8,
                'maxOutputTokens' => 4096,
            ],
        ];

        Log::channel('spotify')->info('Chamando Gemini API', ['payload' => $payload]);

        return $this->callGemini($payload);
    }

    /**
     * Gera uma playlist "na mesma vibe" a partir de uma música de referência e/ou
     * de uma descrição de estilo (JSON format).
     *
     * Substitui o endpoint deprecated /recommendations do Spotify: usa a faixa de
     * referência + a descrição como "semente" para o Gemini. Pelo menos um dos dois
     * deve ser informado.
     *
     * @param  string  $vibe  Descrição livre do estilo/batida desejada (pode ser vazio se houver referência).
     * @param  string  $referenceTrack  Faixa de referência "Nome - Artista" (pode ser vazio se houver vibe).
     * @param  int  $count  Quantidade pedida (buffer p/ sobrar ≥30 após misses/dedup).
     * @param  string  $context  Músicas a excluir (já conhecidas pelo usuário).
     */
    public function generateSimilarPlaylist(string $vibe = '', string $referenceTrack = '', int $count = 36, string $context = ''): array
    {
        $vibe = trim($vibe);
        $referenceTrack = trim($referenceTrack);

        $rules = [
            'Você é um curador musical especialista, com domínio das cenas brasileira e internacional.',
            'INSTRUÇÃO CRÍTICA: responda EXCLUSIVAMENTE com um array JSON válido, sem markdown e sem nenhum texto fora do JSON.',
            "Cada objeto deve ter as chaves 'name' (nome exato da faixa) e 'artist' (artista principal).",
            "Liste EXATAMENTE {$count} músicas REAIS, que de fato existam no Spotify e sejam populares o bastante para aparecer na busca. NUNCA invente títulos, remixes ou artistas.",
        ];

        if ($referenceTrack !== '') {
            $rules[] = "Faixa de referência: \"{$referenceTrack}\". Identifique gênero/subgênero, andamento (BPM), energia, clima, época e idioma dela, e traga músicas que SOEM na mesma pegada — mesma batida e vibe —, não apenas do mesmo artista.";
            $rules[] = 'Mantenha o mesmo idioma e a mesma cena/subgênero da referência, a menos que a descrição peça algo diferente.';
            $rules[] = 'NÃO inclua a própria faixa de referência no resultado.';
        }

        if ($vibe !== '') {
            $rules[] = "Estilo/clima/ocasião descrito pelo usuário: \"{$vibe}\". Respeite isso ao escolher as faixas.";
        }

        $rules[] = 'Não repita músicas e use no máximo 2 faixas por artista, garantindo variedade.';

        if ($context !== '') {
            $rules[] = "NÃO inclua nenhuma destas músicas, que o usuário já conhece: {$context}";
        }

        $systemPrompt = implode("\n", $rules);

        $userLines = [];
        if ($referenceTrack !== '') {
            $userLines[] = "Música de referência: {$referenceTrack}";
        }
        if ($vibe !== '') {
            $userLines[] = "Estilo/vibe desejada: {$vibe}";
        }
        $userLines[] = "Instruções: {$systemPrompt}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => implode("\n", $userLines)],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 8192,
                'responseMimeType' => 'application/json',
            ],
        ];

        Log::channel('spotify')->info('Chamando Gemini API (similar)', ['reference' => $referenceTrack, 'vibe' => $vibe, 'count' => $count]);

        return $this->callGemini($payload);
    }

    /**
     * Busca uma alternativa para uma track específica que o usuário não gostou.
     */
    public function getAlternativeTrack(string $originalPrompt, array $currentTracks): ?array
    {
        $currentStr = collect($currentTracks)->map(fn ($t) => "{$t['name']} by {$t['artist']}")->implode(', ');

        $prompt = "Dê uma alternativa musical para o tema '{$originalPrompt}',
        mas que não seja nenhuma destas: {$currentStr}.
        Retorne APENAS um objeto JSON como {\"name\": \"...\", \"artist\": \"...\"}";

        $result = $this->callGemini([
            'contents' => [['parts' => [['text' => $prompt]]]],
        ]);

        return empty($result) ? null : $result;
    }

    /**
     * Faz a chamada HTTP ao Gemini e extrai o JSON da resposta.
     *
     * Centraliza a montagem da URL (evitando duplicidade de ":generateContent")
     * e a limpeza de markdown, reaproveitado por todos os métodos públicos.
     */
    private function callGemini(array $payload): array
    {
        if (! $this->isConfigured()) {
            Log::channel('spotify')->error('Gemini API Key/modelo não configurado.');

            return [];
        }

        // Status transitórios do Gemini (sobrecarga/limite) que valem nova tentativa.
        $transient = [429, 500, 502, 503, 504];
        $maxAttempts = 2;

        // Tenta cada modelo em ordem; cai para o próximo se o atual ficar indisponível.
        foreach ($this->models as $model) {
            $url = $this->endpoint($model);

            try {
                $response = null;

                for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                    $response = Http::withHeaders(['Content-Type' => 'application/json'])
                        ->connectTimeout(10)
                        ->timeout(25)
                        ->post($url, $payload);

                    if (! in_array($response->status(), $transient, true)) {
                        break;
                    }

                    if ($attempt < $maxAttempts) {
                        Log::channel('spotify')->warning("Gemini '{$model}' indisponível (HTTP {$response->status()}), tentativa {$attempt}/{$maxAttempts}");
                        usleep(800_000); // backoff curto (~0,8s) antes de repetir
                    }
                }

                // Ainda transitório após as tentativas → tenta o próximo modelo.
                if (in_array($response->status(), $transient, true)) {
                    Log::channel('spotify')->warning("Gemini '{$model}' segue indisponível; tentando próximo modelo.");

                    continue;
                }

                if ($response->failed()) {
                    Log::channel('spotify')->error("Erro na API do Gemini ('{$model}'): ", $response->json() ?? ['raw' => $response->body()]);

                    return [];
                }

                $data = $response->json();
                Log::channel('spotify')->info('Resposta Gemini API', ['model' => $model, 'data' => $data]);

                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // Limpa possíveis marcações de markdown (```json ... ```)
                $jsonStr = trim($text);
                if (str_starts_with($jsonStr, '```')) {
                    $jsonStr = preg_replace('/^```(?:json)?|```$/', '', $jsonStr);
                }

                return json_decode(trim($jsonStr), true) ?? [];
            } catch (\Exception $e) {
                Log::channel('spotify')->error("Erro ao chamar Gemini ('{$model}'): ".$e->getMessage());

                continue;
            }
        }

        Log::channel('spotify')->error('Todos os modelos Gemini indisponíveis no momento.');

        return [];
    }
}
