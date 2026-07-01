<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithSpotify;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Mostra o que está tocando agora no Spotify do usuário (faixa, artista e se está tocando).')]
class CurrentlyPlayingTool extends Tool
{
    use InteractsWithSpotify;

    public function handle(Request $request): Response
    {
        try {
            $state = $this->spotify()->getPlaybackState();
        } catch (\Throwable $e) {
            return Response::error($e->getMessage());
        }

        $item = $state['item'] ?? null;

        if (! $item) {
            return Response::text('Nada tocando no momento.');
        }

        return Response::json([
            'is_playing' => $state['is_playing'] ?? false,
            'name' => $item['name'] ?? '',
            'artist' => collect($item['artists'] ?? [])->pluck('name')->implode(', '),
            'uri' => $item['uri'] ?? '',
            'progress_ms' => $state['progress_ms'] ?? null,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
