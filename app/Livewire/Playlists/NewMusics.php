<?php

namespace App\Livewire\Playlists;

use App\Services\SpotifyService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class NewMusics extends Component
{
    private SpotifyService $spotify;

    #[Locked]
    public string $playlistId;
    public string $search;

    public array $playlistInfo = [];
    public array $playlistTracks = [];
    public bool $canEditPlaylist = false;
    public bool $editMusics = false;
    public bool $activeMultipleMusicsToAddPlaylist = false;
    public array $selectedTracks = [];
    public int $musicRepetitive = 0;

    public function __construct()
    {
        $this->spotify = new SpotifyService();
    }

    public function mount($id)
    {
        $this->playlistId = $id;
        $this->playlistInfo = $this->spotify->getInfoPlaylist($this->playlistId);


    }

    public function updatedSearch()
    {
        $this->searchMusics();
    }

    public function searchMusics()
    {
        if (!empty(trim($this->search))) {
            $this->playlistTracks = $this->spotify->searchMusics($this->search);
        } else {
            $this->playlistTracks = [];
        }
    }

    public function toggleTrack($trackId)
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

    public function clearSearch()
    {
        $this->reset(['search']);

        $this->playlistTracks = [];
        $this->activeMultipleMusicsToAddPlaylist = false;
        $this->selectedTracks = [];
    }

    public function activeMultipleMusics()
    {
        $this->activeMultipleMusicsToAddPlaylist = !$this->activeMultipleMusicsToAddPlaylist;

        if ($this->activeMultipleMusicsToAddPlaylist === false) {
            $this->selectedTracks = [];
        }
    }

    public function addMultiplesMusicToPlaylist()
    {
        foreach ($this->selectedTracks as $key => $track) {
            $haveTrackInPlaylist = $this->spotify->countTrackInPlaylist($this->playlistId, $track['uri']);

            if ($haveTrackInPlaylist >= 1) {
                unset($this->selectedTracks[$key]);
            }
        }

        $uris = array_column($this->selectedTracks, 'uri');

        $this->spotify->addMusicsInPlaylist($this->playlistId, $uris);

        return redirect()->route('edit-playlist', $this->playlistId);
    }

    public function addSingleMusicToPlaylist($idTrack = null): void
    {
        if ($idTrack) {
            $track = $this->spotify->countTrackInPlaylist($this->playlistId, $idTrack);

            if ($track >= 1) {
                $this->musicRepetitive = $track;

                $this->dispatch('openmodal');
            } else {
                $this->spotify->addMusicsInPlaylist($this->playlistId, [$idTrack]);
            }
        }
    }

    public function loadMore()
    {
        if (empty(trim($this->search))) {
            return;
        }

        $offset = count($this->playlistTracks['tracks']);
        $total = $this->playlistTracks['total'];

        if ($offset >= $total) {
            return;
        }

        $remaining = $total - $offset;
        $limit = min(50, $remaining);

        $moreMusics = $this->spotify->searchMusics($this->search, 'track', 50, $limit);

        $this->playlistTracks['tracks'] = array_merge($this->playlistTracks['tracks'], $moreMusics['tracks']);
        $this->playlistTracks['offset'] = $moreMusics['offset'];
        $this->playlistTracks['nextUrl'] = $moreMusics['nextUrl'];
        $this->playlistTracks['limit'] = $moreMusics['limit'];
        $this->playlistTracks['total'] = $moreMusics['total'];
    }


    public function handleTrackSelection(string $uri, bool $checked): void
    {
        if (!$checked) {
            $this->selectedTracks = array_values(array_filter(
                $this->selectedTracks,
                fn ($track) => $track['uri'] !== $uri
            ));
        }
    }

    public function render()
    {
        return view('livewire.playlists.new-musics');
    }
}
