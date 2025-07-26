<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSpotify extends Model
{
    protected $table = 'users_spotify';

    protected $fillable = [
        'user_id',
        'external_urls',
        'href_profile',
        'product',
        'avatar',
        'accessToken',
        'country'
    ];

    protected $casts = [
        'accessToken' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
