<?php

use App\Http\Controllers\AuthProvidersController;
use App\Services\ImageS3;
use Illuminate\Support\Facades\Route;

Route::view('/', 'spotify.home-page')->name('login');
Route::view('/privacy', 'spotify.home-page')->name('login');
Route::view('/terms', 'spotify.home-page')->name('login');

Route::prefix('oauth2')->group(function () {
    Route::get('spotify', [AuthProvidersController::class, 'spotifyAuth'])->middleware('web')->name('spotify-auth');
    Route::get('spotify/callback', [AuthProvidersController::class, 'spotifyCallback'])->middleware('web');
});

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'spotify.dashboard')->name('dashboard');
    Route::view('playlist/{id}', 'spotify.view-playlist')->name('edit-playlist');
    Route::view('playlist/{id}/novas-musicas', 'spotify.new-musics-playlist')->name('new-musics-playlist');
});

Route::post('logout', [AuthProvidersController::class, 'logout'])->name('logout');
