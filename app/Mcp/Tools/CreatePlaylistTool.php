<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithSpotify;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Cria uma playlist no Spotify do usuário e, opcionalmente, adiciona faixas por URI.')]
class CreatePlaylistTool extends Tool
{
    use InteractsWithSpotify;

    public function handle(Request $request): Response
    {
        $name = trim((string) $request->get('name'));
        $description = trim((string) ($request->get('description') ?? ''));
        $uris = array_values(array_filter((array) ($request->get('uris') ?? [])));

        if ($name === '') {
            return Response::error('Informe o nome da playlist em "name".');
        }

        try {
            $spotify = $this->spotify();
            $spotifyId = Auth::user()->spotify_id;

            $playlistId = $description !== ''
                ? $spotify->createPlaylist($spotifyId, $name, $description)
                : $spotify->createPlaylist($spotifyId, $name);

            if (! $playlistId) {
                return Response::error('Não foi possível criar a playlist no Spotify.');
            }

            if (! empty($uris)) {
                $spotify->addMusicsInPlaylist($playlistId, $uris);
            }
        } catch (\Throwable $e) {
            return Response::error($e->getMessage());
        }

        return Response::json([
            'id' => $playlistId,
            'url' => "https://open.spotify.com/playlist/{$playlistId}",
            'added' => count($uris),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Nome da playlist.')
                ->required(),
            'description' => $schema->string()
                ->description('Descrição da playlist (opcional).'),
            'uris' => $schema->array()
                ->description('Lista de URIs de faixas (ex.: "spotify:track:...") para adicionar à playlist.')
                ->items($schema->string()),
        ];
    }
}
