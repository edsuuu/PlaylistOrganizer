<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithSpotify;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Toca uma faixa no Spotify do usuário a partir do URI (precisa de um dispositivo ativo).')]
class PlayTrackTool extends Tool
{
    use InteractsWithSpotify;

    public function handle(Request $request): Response
    {
        $uri = trim((string) $request->get('uri'));

        if ($uri === '') {
            return Response::error('Informe o URI da faixa em "uri" (ex.: spotify:track:...).');
        }

        try {
            $ok = $this->spotify()->playTrack($uri);
        } catch (\Throwable $e) {
            return Response::error($e->getMessage());
        }

        return $ok
            ? Response::text('Reproduzindo a faixa.')
            : Response::error('Não foi possível reproduzir. Verifique se há um dispositivo Spotify ativo.');
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'uri' => $schema->string()
                ->description('URI da faixa no Spotify, ex.: spotify:track:4iV5W9uYEdYUVa79Axb7Rh.')
                ->required(),
        ];
    }
}
