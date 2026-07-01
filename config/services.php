<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'spotify' => [
        'url' => env('SPOTIFY_API_URL'),
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        'redirect' => env('SPOTIFY_REDIRECT_URI'),
        // Usuário (e-mail) que o servidor MCP usa para agir no Spotify.
        // Se vazio, usa o primeiro usuário com conta Spotify vinculada.
        'mcp_user_email' => env('MCP_SPOTIFY_USER_EMAIL'),
    ],
    'google' => [
        'gemini' => [
            'key' => env('GOOGLE_GEMINI_KEY'),
            'url' => env('GOOGLE_GEMINI_URL'),
            'model' => env('GOOGLE_GEMINI_MODEL', 'gemini-2.5-flash'),
            'fallback_model' => env('GOOGLE_GEMINI_FALLBACK_MODEL', 'gemini-2.0-flash'),
        ],
    ],

];
