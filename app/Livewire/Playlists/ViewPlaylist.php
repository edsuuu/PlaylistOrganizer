<?php

namespace App\Livewire\Playlists;

use App\Services\SpotifyService;
use App\Traits\WithUIEvents;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ViewPlaylist extends Component
{
    use WithUIEvents;

    private SpotifyService $spotify;

    #[Locked]
    public string $playlistId;

    public array $playlistInfo = [];
    public array $playlistTracks = [];
    public bool $canEditPlaylist = false;
    public bool $editMusics = false;
    public array $selectedTracks = [];

    public function __construct()
    {
        $this->spotify = new SpotifyService();
    }

    public function mount($id)
    {
        $this->playlistId = $id;
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

    public function toggleTrack($trackId, $index)
    {
        if (in_array($trackId, $this->selectedTracks)) {
            $this->selectedTracks = array_filter($this->selectedTracks, fn($id) => $id !== $trackId);
        } else {
            $this->selectedTracks[] = [
                'id' => $trackId,
                'index' => $index
            ];
        }
    }

    public function deleteSelectedTracks()
    {
        dd($this->selectedTracks);
    }


    public function loadMore()
    {
        $offset = count($this->playlistTracks['tracks']);
        $total = $this->playlistTracks['total'];

        if ($offset >= $total) {
            return;
        }

        $remaining = $total - $offset;
        $limit = min(100, $remaining);

        $moreMusics = $this->spotify->getTracksPlaylist($this->playlistId, $offset, $limit);

        $this->playlistTracks['tracks'] = array_merge($this->playlistTracks['tracks'], $moreMusics['tracks']);
        $this->playlistTracks['offset'] = $moreMusics['offset'];
        $this->playlistTracks['nextUrl'] = $moreMusics['nextUrl'];
        $this->playlistTracks['limit'] = $moreMusics['limit'];
        $this->playlistTracks['total'] = $moreMusics['total'];
    }

    public function openModalFavoriteMusics(): void
    {
        $arguments = ['playlistId' => $this->playlistId];
        self::openModalRight($this, FavoritesMusic::class, $arguments, 'Músicas Curtidas');
    }

    public function render()
    {
        return view('livewire.playlists.view-playlist');
    }
}
