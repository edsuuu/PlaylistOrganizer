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

    public $favoriteMusic = false;

    public function __construct()
    {
        $this->spotify = new SpotifyService();
    }

    public function mount($id, $favoritesMusic = false)
    {
        $this->playlistId = $id;
        $this->playlistInfo = $this->spotify->getInfoPlaylist($this->playlistId);
        $this->favoriteMusic = $favoritesMusic;

        if ($favoritesMusic) {
            $this->getFavoriteMusics();
        }
    }

    public function getFavoriteMusics()
    {
        $this->playlistTracks = $this->spotify->getFavoriteMusics();
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
        if (!$this->favoriteMusic && empty(trim($this->search))) {
            return;
        }

        $offset = count($this->playlistTracks['tracks']);
        $total = $this->playlistTracks['total'];

        if ($offset >= $total) {
            return;
        }

        $remaining = $total - $offset;
        $limit = min(50, $remaining);

        if ($this->favoriteMusic) {
            $moreMusics = $this->spotify->getFavoriteMusics($offset, $limit);
        } else {
            $moreMusics = $this->spotify->searchMusics($this->search, 'track', 50, $limit);
        }

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

    public function getMusicOnlyType()
    {
        set_time_limit(300);

        while (
            isset($this->playlistTracks['total']) &&
            isset($this->playlistTracks['tracks']) &&
            count($this->playlistTracks['tracks']) < $this->playlistTracks['total']
        ) {
            $this->loadMore();
        }

        $neighbourhoodMusics = [];
        $monkeysMusics = [];
        $tracks = $this->playlistTracks['tracks'] ?? [];

        foreach ($tracks as $music) {
            if (isset($music['artist'])) {
                if (stripos($music['artist'], 'The Neighbourhood') !== false) {
                    $neighbourhoodMusics[] = $music;
                } elseif (stripos($music['artist'], 'Arctic Monkeys') !== false) {
                    $monkeysMusics[] = $music;
                }
            }
        }

        $neighbourhoodUris = array_column($neighbourhoodMusics, 'uri');
        $monkeysUris = array_column($monkeysMusics, 'uri');

        $allUris = array_merge($neighbourhoodUris, $monkeysUris);

        if (!empty($allUris)) {
            $allUris = array_unique($allUris);

            $urisToAdd = [];
            foreach ($allUris as $uri) {
                $haveTrackInPlaylist = $this->spotify->countTrackInPlaylist($this->playlistId, $uri);
                if ($haveTrackInPlaylist == 0) {
                    $urisToAdd[] = $uri;
                }
            }

            if (!empty($urisToAdd)) {
                foreach (array_chunk($urisToAdd, 100) as $chunk) {
                    $this->spotify->addMusicsInPlaylist($this->playlistId, $chunk);
                }
            }
        }

        return redirect()->route('edit-playlist', $this->playlistId);
    }

    public function render()
    {
        return view('livewire.playlists.new-musics');
    }
}
