<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['track']['id'] ?? null,
            'name' => $this['track']['name'] ?? null,
            'duration_ms' => $this['track']['duration_ms'] ?? 0,
            'artist' => $this['track']['artists'][0]['name'] ?? null,
            'album' => $this['track']['album']['name'] ?? null,
            'image' => $this['track']['album']['images'][0]['url'] ?? null,
            'preview_url' => $this['track']['preview_url'] ?? null, // Added missing preview_url
            'added_at' => $this['added_at'] ?? null,
            'added_at_formatted' => $this->formatAddedAt($this['added_at'] ?? null),
            'uri' => $this['track']['uri'] ?? null,
            
            // Compatibility / Deprecated aliases
            'artists_name' => $this['track']['artists'][0]['name'] ?? null,
            'album_name' => $this['track']['album']['name'] ?? null,
            'album_image' => $this['track']['album']['images'][0]['url'] ?? null,
            'added_at_formated' => $this->formatAddedAt($this['added_at'] ?? null),
        ];
    }

    protected function formatAddedAt(?string $date): ?string
    {
        if (!$date) return null;

        $dt = Carbon::parse($date)->locale('pt_BR');

        if ($dt->diffInDays(now()) <= 14) {
            return $dt->diffForHumans();
        }

        return $dt->translatedFormat('d \d\e M \d\e Y');
    }

    protected function formatDuration(int $ms): string
    {
        $minutes = floor($ms / 60000);
        $seconds = floor(($ms % 60000) / 1000);
        return sprintf('%d:%02d', $minutes, $seconds);
    }

}
