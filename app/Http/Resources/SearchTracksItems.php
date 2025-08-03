<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchTracksItems extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'] ?? null,
            'name' => $this['name'] ?? null,
            'duration_ms' => $this->formatDuration($this['duration_ms'] ?? 0),
            'artists_name' => $this['artists'][0]['name'] ?? null,
            'album_id' => $this['album']['id'] ?? null,
            'album_name' => $this['album']['name'] ?? null,
            'album_image' => $this['album']['images'][0]['url'] ?? null,
            'uri' => $this['uri'] ?? null,
        ];
    }

    protected function formatDuration(int $ms): string
    {
        $minutes = floor($ms / 60000);
        $seconds = floor(($ms % 60000) / 1000);
        return sprintf('%d:%02d', $minutes, $seconds);
    }

}
