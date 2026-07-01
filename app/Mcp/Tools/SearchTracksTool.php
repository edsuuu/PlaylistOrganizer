<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithSpotify;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Busca faixas no Spotify por nome/artista e retorna nome, artista e URI de cada uma.')]
class SearchTracksTool extends Tool
{
    use InteractsWithSpotify;

    public function handle(Request $request): Response
    {
        $query = trim((string) $request->get('query'));
        $limit = max(1, min((int) ($request->get('limit') ?? 10), 50));

        if ($query === '') {
            return Response::error('Informe um termo de busca em "query".');
        }

        try {
            $result = $this->spotify()->searchMusics($query, 'track', $limit);
        } catch (\Throwable $e) {
            return Response::error($e->getMessage());
        }

        $tracks = collect($result['tracks'] ?? [])->map(fn ($track) => [
            'id' => $track['id'] ?? null,
            'name' => $track['name'] ?? '',
            'artist' => $track['artist'] ?? '',
            'uri' => $track['uri'] ?? '',
        ])->values()->all();

        return Response::json([
            'count' => count($tracks),
            'tracks' => $tracks,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('Termo de busca (ex.: "Envolvimento MC Loma" ou "rock anos 80").')
                ->required(),
            'limit' => $schema->integer()
                ->description('Quantidade de faixas a retornar (1 a 50). Padrão 10.')
                ->min(1)
                ->max(50),
        ];
    }
}
