<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchTracks extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'offset' => $this['tracks']['offset'] ?? 0,
            'nextUrl' => $this['tracks']['next'] ?? null,
            'limit' => $this['tracks']['limit'] ?? 0,
            'total' => $this['tracks']['total'] ?? 0,
            'tracks' => SearchTracksItems::collection($this['tracks']['items'] ?? [])->resolve(),
        ];
    }
}
