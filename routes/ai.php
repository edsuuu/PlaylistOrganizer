<?php

use App\Mcp\Servers\SpotifyServer;
use Laravel\Mcp\Facades\Mcp;

// Servidor MCP local (stdio): inicie com `php artisan mcp:start spotify`.
Mcp::local('spotify', SpotifyServer::class);
