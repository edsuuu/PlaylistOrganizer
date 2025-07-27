<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistResource extends JsonResource
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
            'owner' => $this['owner']['id'] ?? null,
            'owner_name' => $this['owner']['display_name'] ?? null,
            'collaborative' => $this['collaborative'] ?? null,
            'public' => $this['public'] ?? null,
            'image' => $this['images'][0]['url'] ?? null,
            'tracks_total' => $this['tracks']['total'] ?? 0,
            'snapshot_id' => $this['snapshot_id'] ?? null,
        ];
    }
}
