<?php

namespace App\Livewire;

use App\Services\SpotifyService;
use Livewire\Component;

class Player extends Component
{
    public array|null $playback = null;

    public function loadPlayback()
    {
        $spotify = new SpotifyService();
        $this->playback = $spotify->getPlaybackState();
    }

    public function formatDuration(int $ms): string
    {
        $minutes = floor($ms / 60000);
        $seconds = floor(($ms % 60000) / 1000);
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function render()
    {
        $this->loadPlayback();

        return view('livewire.player');
    }
}
