<?php

namespace App\Services;

use App\Http\Resources\PlaylistResource;
use App\Http\Resources\SearchTracks;
use App\Http\Resources\TracksListResource;
use App\Models\UserSpotify;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory;
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
        if (!$user || !$user->spotify) {
            throw new \Exception("Usuário não autenticado ou sem conta Spotify vinculada.");
        }

        $this->token = $user->spotify->token;
        $this->userId = $user->id;

        if (Carbon::parse($user->spotify->expires_token)->lessThan(Carbon::now())) {
            $this->refreshToken($user->spotify->refresh_token);
        }

        $this->api = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->baseUrl(Config::get('services.spotify.url'));
    }

    public function getInfoPlaylist($id)
    {
        try {
            $data = $this->api->get("playlists/$id?fields=id,name,public,collaborative,owner(display_name,id),images,uri,snapshot_id,tracks(total)")->json();
            return (new PlaylistResource($data))->toArray(Request::capture());
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao tentar obter informações da playlist: " . $e->getMessage());
            return [];
        }
    }

    public function getTracksPlaylist($id, $offset = 0, $limit = 100)
    {
        try {
            $data = $this->api->get("playlists/$id/tracks?offset=$offset&limit=$limit")->json();
            return (new TracksListResource($data))->toArray(Request::capture());
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao tentar obter tracks da playlist: " . $e->getMessage());
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
            } while (!empty($data['next']));

            return $count;
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao contar tracks na playlist: " . $e->getMessage());
            return 0;
        }
    }

    public function getMePlaylist()
    {
        try {
            $data = $this->api->get('me/playlists')->json();
            return $data['items'] ?? [];
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao tentar trazer playlists: " . $e->getMessage());
            return [];
        }
    }

    public function getFavoriteMusics($offset = 0, $limit = 50)
    {
        try {
            $data = $this->api->get("me/tracks?limit=$limit&offset=$offset")->json();
            return (new TracksListResource($data))->toArray(Request::capture());
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao obter músicas favoritas: " . $e->getMessage());
            return [];
        }
    }

    public function addMusicsInPlaylist(string $playlistId, array $tracks)
    {
        try {
            $data = $this->api->post("playlists/$playlistId/tracks", [
                'uris' => $tracks,
                'position' => 0
            ]);

            Log::channel('spotify')->info("Add na playlist: " . json_encode($data->json()));
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao tentar adicionar musicas na playlist: " . $e->getMessage());
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

            $url = 'search?' . http_build_query($queryParams);

            $data = $this->api->get($url)->json();

            return (new SearchTracks($data))->toArray(Request::capture());
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao pesquisar músicas: " . $e->getMessage());
            return [];
        }
    }

    public function removeMusicsFromPlaylist(string $playlistId, string $snapshotId, array $tracks)
    {
        try {
            $data = $this->api->delete("playlists/$playlistId/tracks", [
                'snapshot_id' => $snapshotId,
                'tracks' => $tracks
            ]);

            Log::channel('spotify')->info("remove music na playlist: " . json_encode($data->json()));

        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao tentar removeMusicsFromPlaylist na playlist: " . $e->getMessage());
        }
    }

    public function createPlaylist($spotifyId, $name)
    {
        try {
            $data = $this->api->post("users/$spotifyId/playlists", [
                'name' => $name,
                'description' => 'Playlist criada pelo PlaylistOrganizer',
            ]);

            return $data->json()['id'] ?? false;
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao criar playlist: " . $e->getMessage());
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
            Log::channel('spotify')->error("Erro ao remover músicas favoritas: " . $e->getMessage());
            return false;
        }
    }

    public function getPlaybackState()
    {
        try {
            $data = $this->api->get("/me/player");
            return $data->json();
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao obter estado do player: " . $e->getMessage());
            return [];
        }
    }

    public function playTrack(string $uri): bool
    {
        try {
            $response = $this->api->put("me/player/play", [
                'uris' => [$uri]
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao reproduzir música: " . $e->getMessage());
            return false;
        }
    }

    public function pause(): bool
    {
        try {
            return $this->api->put("me/player/pause")->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao pausar: " . $e->getMessage());
            return false;
        }
    }

    public function resume(): bool
    {
        try {
            return $this->api->put("me/player/play")->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao retomar: " . $e->getMessage());
            return false;
        }
    }

    public function skipNext(): bool
    {
        try {
            return $this->api->post("me/player/next")->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao pular música: " . $e->getMessage());
            return false;
        }
    }

    public function skipPrevious(): bool
    {
        try {
            return $this->api->post("me/player/previous")->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao voltar música: " . $e->getMessage());
            return false;
        }
    }

    public function setVolume(int $percent): bool
    {
        try {
            return $this->api->put("me/player/volume?volume_percent=$percent")->successful();
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao ajustar volume: " . $e->getMessage());
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
            Log::channel('spotify')->error("Erro ao favoritar música: " . $e->getMessage());
            return false;
        }
    }

    public function checkTracksIsLiked(array $ids): array
    {
        try {
            $idsString = implode(',', $ids);
            $response = $this->api->get("me/tracks/contains?ids=$idsString");
            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao verificar curtidas: " . $e->getMessage());
            return array_fill(0, count($ids), false);
        }
    }

    private function refreshToken($refreshToken): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(Config::get('services.spotify.client_id') . ':' . Config::get('services.spotify.client_secret')),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => Config::get('services.spotify.client_id'),
            ])->json();

            Log::channel('spotify')->info("Refresh token: " . Carbon::now());

            UserSpotify::query()
                ->where('user_id', $this->userId)
                ->update([
                    'token' => $response['access_token'] ?? $this->token,
                    'refresh_token' => $response['refresh_token'] ?? $refreshToken,
                    'expires_token' => Carbon::now()->addSeconds($response['expires_in'] ?? 3600),
                ]);

            $this->token = $response['access_token'] ?? $this->token;
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao atualizar token: " . $e->getMessage());
        }
    }

}
