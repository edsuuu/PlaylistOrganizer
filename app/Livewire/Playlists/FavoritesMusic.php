<?php

namespace App\Livewire\Playlists;

use App\Services\SpotifyService;
use Livewire\Attributes\Url;
use Livewire\Component;

class FavoritesMusic extends Component
{
    #[Url('id')]
    public string $playlistId;

    public $searchMusic;

    public array $musics = [];

    private SpotifyService $spotify;

    public array $playlists = [];
    public array $selectedTracks = [];

    public int $musicRepetitive = 0;
    public bool $activeMultipleMusicsToAddPlaylist = false;

    public function __construct()
    {
        $this->spotify = new SpotifyService();
    }

    public function mount()
    {
        $this->getFavoriteMusics();
    }

    public function addSingleMusicToPlaylist($idTrack = null): void
    {
        if ($idTrack) {
            $track = $this->spotify->countTrackInPlaylist($this->playlistId, $idTrack);

            if ($track > 1) {
                $this->musicRepetitive = $track;

                $this->dispatch('openmodal');
            } else {
                dd('add musica a playlist');
            }
        }
    }

    public function addMultiplesMusicToPlaylist()
    {
        dd($this->selectedTracks);
    }

    public function toggleTrack($id)
    {
        if (in_array($id, $this->selectedTracks, true)) {
            $this->selectedTracks = array_filter($this->selectedTracks, fn($trackId) => $trackId !== $id);
        } else {
            $this->selectedTracks[] = $id;
        }
    }
    public function getFavoriteMusics()
    {
        $this->musics = $this->spotify->getFavoriteMusics();
    }

    public function loadMore()
    {
        $offset = count($this->musics['tracks']);
        $total = $this->musics['total'];

        if ($offset >= $total) {
            return;
        }

        $remaining = $total - $offset;
        $limit = min(50, $remaining);

        $moreMusics = $this->spotify->getFavoriteMusics($offset, $limit);

        $this->musics['tracks'] = array_merge($this->musics['tracks'], $moreMusics['tracks']);
        $this->musics['offset'] = $moreMusics['offset'];
        $this->musics['nextUrl'] = $moreMusics['nextUrl'];
        $this->musics['limit'] = $moreMusics['limit'];
        $this->musics['total'] = $moreMusics['total'];
    }

    public function activeMultipleMusics()
    {
        $this->activeMultipleMusicsToAddPlaylist = !$this->activeMultipleMusicsToAddPlaylist;
    }

    public function render()
    {
        return view('livewire.playlists.favorites-music');
    }
}
