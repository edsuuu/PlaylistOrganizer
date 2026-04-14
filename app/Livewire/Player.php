<?php

namespace App\Livewire;

use App\Services\SpotifyService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class Player extends Component
{
    public array|null $playback = null;
    private SpotifyService $spotify;

    public function boot(): void
    {
        $this->spotify = new SpotifyService();
    }

    #[On('playbackUpdated')]
    public function forceRefresh(): void
    {
        $this->loadPlayback();
    }

    /**
     * Carrega o estado atual da reprodução do Spotify.
     */
    public function loadPlayback(): void
    {
        try {
            $state = $this->spotify->getPlaybackState();
            
            // Verificamos se há conteúdo válido, caso contrário setamos como null
            if ($state && isset($state['item'])) {
                $this->playback = $state;
            } else {
                $this->playback = null;
            }
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao carregar playback no component: " . $e->getMessage());
            $this->playback = null;
        }
    }

    public function pause(): void
    {
        $this->spotify->pause();
        $this->loadPlayback();
    }

    public function resume(): void
    {
        $this->spotify->resume();
        $this->loadPlayback();
    }

    public function next(): void
    {
        $this->spotify->skipNext();
        $this->loadPlayback();
    }

    public function previous(): void
    {
        $this->spotify->skipPrevious();
        $this->loadPlayback();
    }

    public function setVolume(int $percent): void
    {
        $this->spotify->setVolume($percent);
        $this->loadPlayback();
    }

    /**
     * Formata milissegundos em MM:SS.
     */
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
