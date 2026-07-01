<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreatePlaylistTool;
use App\Mcp\Tools\CurrentlyPlayingTool;
use App\Mcp\Tools\PlayTrackTool;
use App\Mcp\Tools\SearchTracksTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Spotify (PlaylistOrganizer)')]
#[Version('0.1.0')]
#[Instructions('Ferramentas para controlar o Spotify do usuário usando a conta vinculada no PlaylistOrganizer: buscar faixas, ver o que está tocando, criar playlists e tocar faixas.')]
class SpotifyServer extends Server
{
    protected array $tools = [
        SearchTracksTool::class,
        CurrentlyPlayingTool::class,
        CreatePlaylistTool::class,
        PlayTrackTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
