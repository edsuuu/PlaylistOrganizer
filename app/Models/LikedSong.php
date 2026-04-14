<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LikedSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'spotify_id',
        'name',
        'artist',
        'album',
        'image',
        'duration_ms',
        'preview_url',
        'uri',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
