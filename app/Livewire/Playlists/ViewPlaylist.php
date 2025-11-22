<?php

namespace App\Livewire\Playlists;

use App\Services\SpotifyService;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ViewPlaylist extends Component
{
    private SpotifyService $spotify;

    #[Locked]
    public string|null $playlistId;

    public array $playlistInfo = [];
    public array $playlistTracks = [];
    public bool $canEditPlaylist = false;
    public bool $editMusics = false;
    public array $selectedTracks = [];
    public bool $favoritePlaylist = false;

    public function __construct()
    {
        $this->spotify = new SpotifyService();
    }

    public function mount($id = null, $favoritePlaylist = false): void
    {
        $this->favoritePlaylist = $favoritePlaylist;

        $this->playlistId = $id;
        $this->getPlaylist();
    }

    #[On('refreshPlaylist')]
    public function getPlaylist(): void
    {
        if ($this->favoritePlaylist) {
            $this->getFavoriteMusics();
        } else {
            $this->getInfoPlaylist();
            $this->getTracks();
        }
    }


    public function getFavoriteMusics()
    {
        $this->playlistTracks = $this->spotify->getFavoriteMusics();
    }

    public function getInfoPlaylist(): void
    {
        $this->playlistInfo = $this->spotify->getInfoPlaylist($this->playlistId);
        if (isset($this->playlistInfo)) {
            if ($this->playlistInfo['collaborative'] === true || auth()->user()->spotify_id === $this->playlistInfo['owner']) {
                $this->canEditPlaylist = true;
            }
        }
    }

    public function getTracks(): void
    {
        $tracks = $this->spotify->getTracksPlaylist($this->playlistId);
        $this->playlistTracks = $tracks;
    }

    public function toggleDelete(): void
    {
        $this->editMusics = !$this->editMusics;
        $this->selectedTracks = [];
    }

    public function toggleTrack($trackId): void
    {
        foreach ($this->selectedTracks as $track) {
            if ($track['uri'] === $trackId) {
                return;
            }
        }

        $this->selectedTracks[] = [
            'uri' => $trackId
        ];
    }

    public function deleteSingleTrack(string $uri): void
    {
        $this->selectedTracks[] = [
            'uri' => $uri
        ];

        $this->deleteSelectedTracks();
    }

    public function deleteSelectedTracks(): void
    {
        $this->spotify->removeMusicsFromPlaylist($this->playlistInfo['id'], $this->playlistInfo['snapshot_id'], $this->selectedTracks);
        $this->editMusics = false;
        $this->selectedTracks = [];
        $this->getPlaylist();
        $this->dispatch('refreshPlaylistsUser');
    }


    public function loadMore(): void
    {
        $offset = count($this->playlistTracks['tracks']);
        $total = $this->playlistTracks['total'];

        if ($offset >= $total) {
            return;
        }

        $remaining = $total - $offset;

        $min = $this->favoritePlaylist ? 50 : 100;

        $limit = min($min, $remaining);

        if ($this->favoritePlaylist) {
            $moreMusics = $this->spotify->getFavoriteMusics($offset, $limit);
        } else {
            $moreMusics = $this->spotify->getTracksPlaylist($this->playlistId, $offset, $limit);
        }

        $this->playlistTracks['tracks'] = array_merge($this->playlistTracks['tracks'], $moreMusics['tracks']);
        $this->playlistTracks['offset'] = $moreMusics['offset'];
        $this->playlistTracks['nextUrl'] = $moreMusics['nextUrl'];
        $this->playlistTracks['limit'] = $moreMusics['limit'];
        $this->playlistTracks['total'] = $moreMusics['total'];
    }

    public function render()
    {
        return view('livewire.playlists.view-playlist');
    }
}
