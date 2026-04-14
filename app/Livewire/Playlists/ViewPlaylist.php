<?php

namespace App\Livewire\Playlists;

use App\Services\SpotifyService;
use Illuminate\Support\Facades\Log;
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
    public array $duplicates = [];
    public bool $isChecking = false;
    public array $duplicatePositions = [];

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

    /**
     * Identifica músicas duplicadas na playlist por ID ou Nome.
     */
    public function checkDuplicates(): void
    {
        $this->isChecking = true;
        $this->duplicates = [];
        
        $allTracks = [];
        $offset = 0;
        $limit = 100;
        $total = $this->playlistInfo['tracks']['total'] ?? 0;

        try {
            // Se a playlist for muito grande, pegamos tudo em lotes
            do {
                $response = $this->spotify->getTracksPlaylist($this->playlistId, $offset, $limit);
                $batch = $response['tracks'] ?? [];
                
                // Adicionamos a posição absoluta para remoção precisa depois
                foreach ($batch as $index => $track) {
                    $track['position'] = $offset + $index;
                    $allTracks[] = $track;
                }

                $offset += $limit;
            } while ($offset < $total && !empty($batch));

            $seenIds = [];
            $seenNames = [];

            foreach ($allTracks as $track) {
                $id = $track['id'] ?? null;
                $name = mb_strtolower($track['name'] ?? '');
                $artist = mb_strtolower($track['artist'] ?? '');
                
                $isDuplicate = false;
                $reason = '';

                $compositeKey = "{$name}|{$artist}";

                if ($id && isset($seenIds[$id])) {
                    $isDuplicate = true;
                    $reason = 'ID Duplicado';
                } elseif ($compositeKey && isset($seenNames[$compositeKey])) {
                    $isDuplicate = true;
                    $reason = 'Nome e Artista Duplicados';
                }

                if ($isDuplicate) {
                    $this->duplicates[] = [
                        'id' => $id,
                        'name' => $track['name'],
                        'artist' => $track['artist'],
                        'image' => $track['image'],
                        'uri' => $track['uri'],
                        'position' => $track['position'],
                        'reason' => $reason
                    ];
                }

                if ($id) $seenIds[$id] = true;
                if ($compositeKey) $seenNames[$compositeKey] = true;
            }
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao validar duplicatas: " . $e->getMessage());
        }

        $this->isChecking = false;
        $this->dispatch('show-duplicates');
    }

    /**
     * Remove as duplicatas selecionadas usando suas posições.
     */
    public function removeSelectedDuplicates(): void
    {
        if (empty($this->duplicatePositions)) return;

        $tracksToRemove = [];
        foreach ($this->duplicatePositions as $pos) {
            // Encontra a URI nos duplicados
            $dup = collect($this->duplicates)->firstWhere('position', $pos);
            if ($dup) {
                $tracksToRemove[] = [
                    'uri' => $dup['uri'],
                    'positions' => [(int)$pos]
                ];
            }
        }

        if (!empty($tracksToRemove)) {
            $this->spotify->removeMusicsFromPlaylist($this->playlistId, $this->playlistInfo['snapshot_id'], $tracksToRemove);
            $this->duplicates = [];
            $this->duplicatePositions = [];
            $this->getPlaylist();
            $this->dispatch('refreshPlaylistsUser');
            session()->flash('message', 'Duplicadas removidas com sucesso!');
        }
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

    public function play(string $uri): void
    {
        $this->spotify->playTrack($uri);
        $this->dispatch('playbackUpdated');
    }

    public function render()
    {
        return view('livewire.playlists.view-playlist');
    }
}
