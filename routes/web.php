<?php

use App\Http\Controllers\AuthProvidersController;
use App\Services\ImageS3;
use Illuminate\Support\Facades\Route;


Route::view('/', 'spotify.home-page')->name('home');

Route::middleware('web')->get('oauth2/spotify', [AuthProvidersController::class, 'googleAuth'])->name('google');
Route::middleware('web')->get('oauth2/spotify/callback', [AuthProvidersController::class, 'googleCallback']);

Route::middleware(['auth', 'web'])->group(function () {
    Route::view('dashboard', 'spotify.dashboard')->name('dashboard');

});

Route::get('image/{path}/{id}', [ImageS3::class, 'handle'])->name('image-s3');
Route::post('logout', [AuthProvidersController::class, 'logout'])->name('logout');
