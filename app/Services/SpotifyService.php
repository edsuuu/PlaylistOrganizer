<?php

namespace App\Services;

use App\Http\Resources\PlaylistResource;
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
    /**
     * Create a new class instance.
     */

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
        $data = $this->api->get("playlists/$id?fields=id,name,public,collaborative,owner(display_name,id),images,uri,snapshot_id,tracks(total)")->json();

        return (new PlaylistResource($data))->toArray(request());
    }

    public function getTracksPlaylist($id, $offset = 0, $limit = 100)
    {
        $data = $this->api->get("playlists/$id/tracks?offset=$offset&limit=$limit")->json();
//        dd($data);
        return (new TracksListResource($data))->toArray(request());
    }


    public function getMePlaylist()
    {
//        "https://api.spotify.com/v1/users/user:id/playlists?offset=0&limit=50", para passar querystring offset e limit
        try {
            $data = $this->api->get('me/playlists')->json();
            return $data['items'];
        } catch (\Exception $e) {

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
