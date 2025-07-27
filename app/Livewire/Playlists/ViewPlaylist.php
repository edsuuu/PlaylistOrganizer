<?php

namespace App\Livewire\Playlists;

use App\Services\SpotifyService;
use Livewire\Component;

class ViewPlaylist extends Component
{
    private SpotifyService $spotify;
    private string $playlistId;
    public array $playlistInfo = [];
    public array $playlistTracks = [];
    public bool $canEditPlaylist = false;

    public function mount($id)
    {
        $this->playlistId = $id;
        $this->spotify = new SpotifyService();
        $this->getInfoPlaylist();
        $this->getTracks();
    }

    public function getInfoPlaylist()
    {
        $this->playlistInfo = $this->spotify->getInfoPlaylist($this->playlistId);
        if (isset($this->playlistInfo)) {
            if ($this->playlistInfo['collaborative'] === true || auth()->user()->spotify_id === $this->playlistInfo['owner']) {
                $this->canEditPlaylist = true;
            }
        }
    }

    public function getTracks()
    {
        $tracks =  $this->spotify->getTracksPlaylist($this->playlistId);
        $this->playlistTracks = $tracks;

    }


    public function render()
    {
        return view('livewire.playlists.view-playlist');
    }
}
