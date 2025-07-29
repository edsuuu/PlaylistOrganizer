<?php

namespace App\Livewire;

use App\Services\SpotifyService;
use Livewire\Attributes\On;
use Livewire\Component;

class Sidebar extends Component
{
    private SpotifyService $spotify;

    public array $playlists = [];

    public function __construct()
    {
        $this->spotify = new SpotifyService();
    }


    public function mount()
    {
        $this->getPlaylists();
    }


    #[On('refreshPlaylistsUser')]
    public function getPlaylists(): void
    {
        $this->playlists = $this->spotify->getMePlaylist();
    }


    public function render()
    {
        return view('livewire.sidebar');
    }
}
