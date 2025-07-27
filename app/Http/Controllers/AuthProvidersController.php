<?php

namespace App\Http\Controllers;

use App\Livewire\Actions\Logout;
use App\Models\User;
use App\Models\UserSpotify;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthProvidersController extends Controller
{
    public function spotifyAuth()
    {
        return Socialite::driver('spotify')
            ->scopes([
                'user-read-playback-state',
                'user-modify-playback-state',
                'user-read-currently-playing',
                'playlist-read-private',
                'playlist-read-collaborative',
                'playlist-modify-private',
                'playlist-modify-public',
                'user-read-playback-position',
                'user-top-read',
                'user-read-recently-played',
                'user-read-email',
                'user-library-modify',
                'user-library-read',
                'user-read-private',
            ])
            ->redirect();
    }

    public function spotifyCallback()
    {
        try {
            $spotifyUser = Socialite::driver('spotify')
                ->stateless()
                ->user();

            $userDB = User::query()
                ->with('spotify')
                ->where('email', $spotifyUser->user['email'])
                ->where('spotify_id', $spotifyUser->user['id'])
                ->first();

            if (!isset($userDB)) {
                $userCreate = User::query()->create([
                    'name' => $spotifyUser->user['display_name'],
                    'email' =>  $spotifyUser->user['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(16)),
                    'spotify_id' => $spotifyUser->user['id'],
                ]);

                UserSpotify::query()->create([
                    'user_id' => $userCreate->id,
                    'external_urls' => $spotifyUser->user['external_urls']['spotify'],
                    'href_profile' => $spotifyUser->user['href'],
                    'product' => $spotifyUser->user['product'],
                    'avatar' => $spotifyUser->user['images'][0]['url'],
                    'token' => $spotifyUser->token,
                    'refresh_token' => $spotifyUser->refreshToken,
                    'expires_token' => now()->addSeconds($spotifyUser->expiresIn),
                    'country' => $spotifyUser->user['country'],
                ]);

                Auth::loginUsingId($userCreate->id, true);
            } else {
                UserSpotify::query()
                    ->where('user_id', $userDB->id)
                    ->update([
                        'avatar' => $spotifyUser->user['images'][0]['url'],
                        'product' => $spotifyUser->user['product'],
                        'token' => $spotifyUser->token,
                        'refresh_token' => $spotifyUser->refreshToken,
                        'expires_token' => now()->addSeconds($spotifyUser->expiresIn),
                    ]);

                Auth::login($userDB);
            }
            session()->regenerate();
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::channel('daily')->error($e);
            return redirect('/')->with('error', 'Erro ao autenticar com o Spotify. Tente novamente.');
        }
    }

    public function logout(Logout $logout)
    {
        $logout();
        return redirect('/');
    }
}
