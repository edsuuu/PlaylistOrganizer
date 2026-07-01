<?php

namespace App\Mcp\Concerns;

use App\Models\User;
use App\Services\SpotifyService;
use Illuminate\Support\Facades\Auth;

trait InteractsWithSpotify
{
    /**
     * Resolve o usuário do app com Spotify vinculado e retorna um SpotifyService
     * autenticado como ele (o SpotifyService usa Auth::user() internamente).
     *
     * O usuário é escolhido por MCP_SPOTIFY_USER_EMAIL (se definido) ou o primeiro
     * que tiver conta Spotify vinculada.
     */
    protected function spotify(): SpotifyService
    {
        $email = config('services.spotify.mcp_user_email');

        $user = User::query()
            ->when($email, fn ($query) => $query->where('email', $email))
            ->whereHas('spotify')
            ->first();

        if (! $user) {
            throw new \RuntimeException(
                'Nenhuma conta Spotify vinculada encontrada. Faça login no PlaylistOrganizer ou defina MCP_SPOTIFY_USER_EMAIL no .env.'
            );
        }

        // setUser (e não login) porque o MCP roda em contexto stdio, sem sessão.
        Auth::setUser($user);

        return new SpotifyService;
    }
}
