<?php

namespace App\Livewire;

use App\Services\SpotifyService;
use Livewire\Component;

class Sidebar extends Component
{
    private SpotifyService $spotify;

    public array $playlists = [];

    public function mount()
    {
        $this->spotify = new SpotifyService();

        $this->getPlaylists();
    }


    private function getPlaylists()
    {
        $this->playlists = $this->spotify->getMePlaylist();
    }


    public function render()
    {
        return view('livewire.sidebar');
    }
}
