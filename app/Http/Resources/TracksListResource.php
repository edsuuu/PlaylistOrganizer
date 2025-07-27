<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TracksListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'offset' => $this['offset'] ?? 0,
            'nextUrl' => $this['next'] ?? null,
            'limit' => $this['limit'] ?? 0,
            'total' => $this['total'] ?? 0,
            'tracks' => TrackResource::collection($this['items'] ?? [])->resolve(),
        ];
    }
}
