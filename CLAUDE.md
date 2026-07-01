# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Runtime: use PHP 8.4, not 8.5

The app must run on **PHP 8.4** (Homebrew `php@8.4`). Under PHP 8.5, `config/database.php` emits a `Deprecated: Constant PDO::MYSQL_ATTR_SSL_CA` warning. It's harmless over HTTP, but it prints to STDOUT and **corrupts the MCP server's stdio JSON-RPC** (see MCP section). `~/.zshenv` pins `php@8.4`; the MCP `.mcp.json` calls php@8.4 by absolute path for the same reason.

## Commands

- `composer dev` — main dev loop: runs `php artisan serve`, `queue:listen`, `pail` (live logs) and `npm run dev` (Vite) concurrently.
- `php artisan serve` — app only. `npm run dev` / `npm run build` — Vite + Tailwind v4.
- `php artisan test` (PHPUnit 11) — full suite. Single test: `php artisan test --filter=TestName`.
- `vendor/bin/pint` — code formatter (Laravel preset). Run it on changed PHP files before committing; the codebase is Pint-clean.
- `php artisan pail` — tail logs in real time. `owen-it` audit + `opcodesio/log-viewer` (web UI) also available. Spotify calls log to a dedicated channel at `storage/logs/spotify.log`.
- `php artisan mcp:start spotify` — start the MCP server over stdio (normally launched by the MCP client via `.mcp.json`, not by hand).

## Architecture

Laravel 12 + Livewire 3 + Flux UI. A Spotify client for browsing/creating playlists and discovering new music, plus an in-repo MCP server exposing the same Spotify actions to AI clients. UI text and code comments are in Portuguese (pt-BR).

**Spotify access is centralized in `app/Services/SpotifyService.php`.** Every Spotify API call goes through it. Its constructor reads the current user's OAuth token (`Auth::user()->spotify->token`) and **auto-refreshes** it when expired, so it requires an authenticated user with a linked Spotify account. OAuth is handled by `AuthProvidersController` via Socialite (`socialiteproviders/spotify`); tokens live in the `users_spotify` table (`User hasOne UserSpotify`, via `spotify_id`). When acting outside a web session (e.g. MCP), set the user with `Auth::setUser($user)` before instantiating the service — never `Auth::login()` (no session in stdio).

**AI music discovery** lives in the `AiGenerator` Livewire component (`/ai-generator`). Spotify's `/recommendations` endpoint is deprecated, so recommendations come from one of two engines chosen by a UI toggle:
- `playlists` (default) — pulls tracks from public Spotify playlists matched by reference artist / vibe. No AI, few API calls, no rate limits.
- `ai` — `app/Services/GeminiService.php` generates track names (model + fallback configurable via `services.google.gemini.*`), each resolved via Spotify search.
Both paths funnel through `finalizeTracks()`, which removes tracks the user already liked — by exact id **and** by normalized `title|artist` (a liked song exists under many Spotify ids; the id check alone misses variants). The liked set comes from `SpotifyService::getLikedTrackKeys()` (paginates the real Spotify library, cached 15 min); the local `LikedSong` table is not authoritative (it drifts out of sync).

**MCP server** (`laravel/mcp`) is defined under `app/Mcp/` and registered in `routes/ai.php` (`Mcp::local('spotify', ...)`), exposed to clients via `.mcp.json`. Tools (`SearchTracks`, `CurrentlyPlaying`, `CreatePlaylist`, `PlayTrack`) reuse `SpotifyService` through the `InteractsWithSpotify` trait, which resolves the acting user from `MCP_SPOTIFY_USER_EMAIL` (or the first linked user) and calls `Auth::setUser()`.

## Conventions

- New Spotify functionality goes in `SpotifyService` (reused by both Livewire and MCP) rather than calling the API directly from components/tools.
- Livewire actions that call Spotify + Gemini can exceed the 30s `php artisan serve` limit; long ones call `set_time_limit()`. Blocking actions can't show a server-set `$isGenerating` flag mid-request — drive loading UI with `wire:loading` instead.
- Required config in `.env` (see `.env.example`): `SPOTIFY_*`, `GOOGLE_GEMINI_KEY`/`_URL`/`_MODEL`/`_FALLBACK_MODEL`, optional `MCP_SPOTIFY_USER_EMAIL`.
