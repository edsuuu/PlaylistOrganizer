<?php

namespace App\Services;

use App\Http\Resources\PlaylistResource;
use App\Http\Resources\SearchTracks;
use App\Http\Resources\TracksListResource;
use App\Models\UserSpotify;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Log;

class SpotifyService
{
    private Factory|PendingRequest $api;
    private string $token;
    private int $userId;

    public function __construct()
    {
        $this->token = Auth::user()->spotify->token;
        $this->userId = Auth::user()->id;

        if (Carbon::parse(Auth::user()->spotify->expires_token)->lessThan(now())) {
            $this->refreshToken(Auth::user()->spotify->refresh_token);
        }

        $this->api = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->baseUrl(config('services.spotify.url'));
    }

    public function getInfoPlaylist($id)
    {
        try {
            $data = $this->api->get("playlists/$id?fields=id,name,public,collaborative,owner(display_name,id),images,uri,snapshot_id,tracks(total)")->json();
            return (new PlaylistResource($data))->toArray(request());
        } catch (\Exception $e) {
            Log::channel('spotify')->info("Erro ao tentar obter informações da playlist: " . $e);
            return [];
        }
    }

    public function getTracksPlaylist($id, $offset = 0, $limit = 100)
    {
        try {
            $data = $this->api->get("playlists/$id/tracks?offset=$offset&limit=$limit")->json();
            return (new TracksListResource($data))->toArray(request());
        } catch (\Exception $e) {
            Log::channel('spotify')->info("Erro ao tentar obter tracks da playlist: " . $e);
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
            Log::channel('spotify')->info("Erro ao contar tracks na playlist: " . $e);
            return false;
        }
    }

    public function getMePlaylist()
    {
        try {
            $data = $this->api->get('me/playlists')->json();
            return $data['items'];
        } catch (\Exception $e) {
            Log::channel('spotify')->info("Erro ao tentar trazer playlists" . $e);
            return [];
        }
    }

    public function getFavoriteMusics($offset = 0, $limit = 50)
    {
        try {
            $data = $this->api->get("me/tracks?limit=$limit&offset=$offset")->json();
            return (new TracksListResource($data))->toArray(request());
        } catch (\Exception $e) {
            Log::channel('spotify')->info("Erro ao obter músicas favoritas: " . $e);
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
            Log::channel('spotify')->info("Erro ao tentar adicionar musicas na playlist" . $e);
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

            return (new SearchTracks($data))->toArray(request());
        } catch (\Exception $e) {
            Log::channel('spotify')->info("Erro ao pesquisar músicas: " . $e);
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
            Log::channel('spotify')->info("Erro ao tentar removeMusicsFromPlaylist na playlist" . $e);
        }
    }

    public function createPlaylist($spotifyId, $name)
    {
        try {
            $data = $this->api->post("users/$spotifyId/playlists", [
                'name' => $name,
                'description' => 'Playlist criada pelo PlaylistOrganizer',
            ]);

            return $data->json()['id'];
        } catch (\Exception $e) {
            Log::channel('spotify')->info("Erro ao criar playlist: " . $e);
            return false;
        }
    }

    private function refreshToken($refreshToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(config('services.spotify.client_id') . ':' . config('services.spotify.client_secret')),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => config('services.spotify.client_id'),
            ]);

            Log::channel('spotify')->info("Refresh token: " . now());

            $data = $response->json();

            UserSpotify::query()
                ->where('user_id', $this->userId)
                ->update([
                    'token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? $refreshToken,
                    'expires_token' => now()->addSeconds($data['expires_in']),
                ]);

            $this->token = $data['access_token'];
        } catch (\Exception $e) {
            Log::channel('spotify')->info($e);
        }
    }

}
