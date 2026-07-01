<?php

namespace App\Services;

use App\Http\Resources\PlaylistResource;
use App\Http\Resources\SearchTracks;
use App\Http\Resources\TracksListResource;
use App\Models\UserSpotify;
use Carbon\Carbon;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class SpotifyService
{
    private Factory|PendingRequest $api;

    private string $token;

    private int $userId;

    public function __construct()
    {
        $user = Auth::user();
        if (! $user || ! $user->spotify) {
            throw new \Exception('Usuário não autenticado ou sem conta Spotify vinculada.');
        }

        $this->token = $user->spotify->token;
        $this->userId = $user->id;

        if (Carbon::parse($user->spotify->expires_token)->lessThan(Carbon::now())) {
            $this->refreshToken($user->spotify->refresh_token);
        }

        $this->api = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->baseUrl(Config::get('services.spotify.url'));
    }

    public function getInfoPlaylist($id)
    {
        try {
            $data = $this->api->get("playlists/$id?fields=id,name,public,collaborative,owner(display_name,id),images,uri,snapshot_id,tracks(total)")->json();

            return (new PlaylistResource($data))->toArray(Request::capture());
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao tentar obter informações da playlist: '.$e->getMessage());

            return [];
        }
    }

    public function getTracksPlaylist($id, $offset = 0, $limit = 100)
    {
        try {
            $data = $this->api->get("playlists/$id/tracks?offset=$offset&limit=$limit")->json();

            return (new TracksListResource($data))->toArray(Request::capture());
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao tentar obter tracks da playlist: '.$e->getMessage());

            return [];
        }
    }

    public function countTrackInPlaylist(string $playlistId, string $trackId): int
    {
        try {
            $offset = 0;
            $limit = 100;
            $count = 0;

            do {
                $response = $this->api->get("playlists/{$playlistId}/tracks?offset={$offset}&limit={$limit}");
                $data = $response->json();

                foreach ($data['items'] as $item) {
                    if (($item['track']['uri'] ?? null) === $trackId) {
                        $count++;
                    }
                }

                $offset += $limit;
            } while (! empty($data['next']));

            return $count;
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao contar tracks na playlist: '.$e->getMessage());

            return 0;
        }
    }

    public function getMePlaylist()
    {
        try {
            $data = $this->api->get('me/playlists')->json();

            return $data['items'] ?? [];
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao tentar trazer playlists: '.$e->getMessage());

            return [];
        }
    }

    public function getFavoriteMusics($offset = 0, $limit = 50)
    {
        try {
            $data = $this->api->get("me/tracks?limit=$limit&offset=$offset")->json();

            return (new TracksListResource($data))->toArray(Request::capture());
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao obter músicas favoritas: '.$e->getMessage());

            return [];
        }
    }

    public function addMusicsInPlaylist(string $playlistId, array $tracks)
    {
        try {
            $data = $this->api->post("playlists/$playlistId/tracks", [
                'uris' => $tracks,
                'position' => 0,
            ]);

            Log::channel('spotify')->info('Add na playlist: '.json_encode($data->json()));
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao tentar adicionar musicas na playlist: '.$e->getMessage());
        }
    }

    public function searchMusics($search, $type = 'track', $limit = 50, $offset = 0)
    {
        try {
            $queryParams = [
                'q' => $search,
                'type' => $type,
                'limit' => $limit,
                'offset' => $offset,
            ];

            $url = 'search?'.http_build_query($queryParams);

            $data = $this->api->get($url)->json();

            return (new SearchTracks($data))->toArray(Request::capture());
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao pesquisar músicas: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Busca playlists públicas por um termo (nome/descrição).
     *
     * @return array<int, array{id:string,name:string,owner:string,total:int}>
     */
    public function searchPlaylists(string $query, int $limit = 5): array
    {
        try {
            $url = 'search?'.http_build_query([
                'q' => $query,
                'type' => 'playlist',
                'limit' => $limit,
            ]);

            $data = $this->api->get($url)->json();
            $items = $data['playlists']['items'] ?? [];

            $playlists = [];
            foreach ($items as $p) {
                // O Spotify às vezes devolve itens nulos no array de playlists.
                if (! is_array($p) || empty($p['id'])) {
                    continue;
                }

                $playlists[] = [
                    'id' => $p['id'],
                    'name' => $p['name'] ?? '',
                    'owner' => $p['owner']['display_name'] ?? '',
                    'total' => $p['tracks']['total'] ?? 0,
                ];
            }

            return $playlists;
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao buscar playlists: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Retorna as faixas de uma playlist já no formato usado pela UI.
     *
     * @return array<int, array{id:string,name:string,artist:string,album:string,image:?string,uri:string}>
     */
    public function getPlaylistTracksRaw(string $id, int $limit = 100): array
    {
        try {
            $fields = 'items(track(id,name,uri,type,artists(name),album(name,images)))';
            $url = "playlists/{$id}/tracks?limit={$limit}&fields=".urlencode($fields);

            $data = $this->api->get($url)->json();
            $items = $data['items'] ?? [];

            $tracks = [];
            foreach ($items as $item) {
                $track = $item['track'] ?? null;

                // Ignora itens removidos, episódios de podcast e faixas locais.
                if (! is_array($track) || ($track['type'] ?? 'track') !== 'track' || empty($track['id'])) {
                    continue;
                }

                $tracks[] = [
                    'id' => $track['id'],
                    'name' => $track['name'] ?? '',
                    'artist' => $track['artists'][0]['name'] ?? '',
                    'album' => $track['album']['name'] ?? '',
                    'image' => $track['album']['images'][0]['url'] ?? null,
                    'uri' => $track['uri'] ?? '',
                ];
            }

            return $tracks;
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao obter faixas da playlist: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Busca um artista pelo nome e retorna id + gêneros (para montar buscas).
     *
     * @return array{id:?string,name:string,genres:array<int,string>}|null
     */
    public function getArtistByName(string $name): ?array
    {
        try {
            $url = 'search?'.http_build_query([
                'q' => $name,
                'type' => 'artist',
                'limit' => 1,
            ]);

            $artist = $this->api->get($url)->json()['artists']['items'][0] ?? null;

            if (! is_array($artist)) {
                return null;
            }

            return [
                'id' => $artist['id'] ?? null,
                'name' => $artist['name'] ?? $name,
                'genres' => $artist['genres'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao buscar artista: '.$e->getMessage());

            return null;
        }
    }

    public function removeMusicsFromPlaylist(string $playlistId, string $snapshotId, array $tracks)
    {
        try {
            $data = $this->api->delete("playlists/$playlistId/tracks", [
                'snapshot_id' => $snapshotId,
                'tracks' => $tracks,
            ]);

            Log::channel('spotify')->info('remove music na playlist: '.json_encode($data->json()));

        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao tentar removeMusicsFromPlaylist na playlist: '.$e->getMessage());
        }
    }

    public function createPlaylist($spotifyId, $name, $description = 'Playlist criada pelo PlaylistOrganizer')
    {
        try {
            $data = $this->api->post("users/$spotifyId/playlists", [
                'name' => $name,
                'description' => $description,
            ]);

            return $data->json()['id'] ?? false;
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao criar playlist: '.$e->getMessage());

            return false;
        }
    }

    public function removeFavoriteTracks(array $ids): bool
    {
        try {
            $idsString = implode(',', $ids);
            $response = $this->api->delete("me/tracks?ids={$idsString}");

            return $response->status() === 200;
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao remover músicas favoritas: '.$e->getMessage());

            return false;
        }
    }

    public function getPlaybackState()
    {
        try {
            $data = $this->api->get('/me/player');

            return $data->json();
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao obter estado do player: '.$e->getMessage());

            return [];
        }
    }

    public function playTrack(string $uri): bool
    {
        try {
            $response = $this->api->put('me/player/play', [
                'uris' => [$uri],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao reproduzir música: '.$e->getMessage());

            return false;
        }
    }

    public function pause(): bool
    {
        try {
            return $this->api->put('me/player/pause')->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao pausar: '.$e->getMessage());

            return false;
        }
    }

    public function resume(): bool
    {
        try {
            return $this->api->put('me/player/play')->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao retomar: '.$e->getMessage());

            return false;
        }
    }

    public function skipNext(): bool
    {
        try {
            return $this->api->post('me/player/next')->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao pular música: '.$e->getMessage());

            return false;
        }
    }

    public function skipPrevious(): bool
    {
        try {
            return $this->api->post('me/player/previous')->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao voltar música: '.$e->getMessage());

            return false;
        }
    }

    public function setVolume(int $percent): bool
    {
        try {
            return $this->api->put("me/player/volume?volume_percent=$percent")->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao ajustar volume: '.$e->getMessage());

            return false;
        }
    }

    public function toggleLikeTrack(string $id, bool $state): bool
    {
        try {
            if ($state) {
                return $this->api->put("me/tracks?ids=$id")->successful();
            } else {
                return $this->api->delete("me/tracks?ids=$id")->successful();
            }
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao favoritar música: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Conjunto de chaves "título|artista" das músicas curtidas reais do Spotify
     * (paginado, com cache). Serve para descartar variações da mesma faixa que
     * têm id diferente do id curtido.
     *
     * @return array<string, true>
     */
    public function getLikedTrackKeys(int $maxPages = 40): array
    {
        return Cache::remember("spotify_liked_keys_{$this->userId}", now()->addMinutes(15), function () use ($maxPages) {
            $keys = [];

            for ($page = 0; $page < $maxPages; $page++) {
                $tracks = $this->getFavoriteMusics($page * 50, 50)['tracks'] ?? [];

                foreach ($tracks as $track) {
                    $keys[$this->likedKey($track['name'] ?? '', $track['artist'] ?? '')] = true;
                }

                if (count($tracks) < 50) {
                    break;
                }
            }

            return $keys;
        });
    }

    /**
     * Normaliza "nome|artista" para comparar faixas ignorando id, caixa e
     * sufixos como "(Remaster)" ou "[Live]".
     */
    public function likedKey(string $name, string $artist): string
    {
        $norm = static function (string $value): string {
            $value = mb_strtolower($value);
            $value = preg_replace('/[\(\[].*?[\)\]]/u', '', $value); // remove (...) e [...]
            $value = preg_replace('/\s+/u', ' ', $value);

            return trim($value);
        };

        return $norm($name).'|'.$norm($artist);
    }

    public function checkTracksIsLiked(array $ids): array
    {
        try {
            $idsString = implode(',', $ids);
            $response = $this->api->get("me/tracks/contains?ids=$idsString");

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao verificar curtidas: '.$e->getMessage());

            return array_fill(0, count($ids), false);
        }
    }

    private function refreshToken($refreshToken): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode(Config::get('services.spotify.client_id').':'.Config::get('services.spotify.client_secret')),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => Config::get('services.spotify.client_id'),
            ])->json();

            Log::channel('spotify')->info('Refresh token: '.Carbon::now());

            UserSpotify::query()
                ->where('user_id', $this->userId)
                ->update([
                    'token' => $response['access_token'] ?? $this->token,
                    'refresh_token' => $response['refresh_token'] ?? $refreshToken,
                    'expires_token' => Carbon::now()->addSeconds($response['expires_in'] ?? 3600),
                ]);

            $this->token = $response['access_token'] ?? $this->token;
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao atualizar token: '.$e->getMessage());
        }
    }
}
