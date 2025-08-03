<?php

namespace App\Livewire;

use App\Services\SpotifyService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Dashboard extends Component
{
    public function createNewPlaylist()
    {
        try {
            $spotify = new SpotifyService();

            $name = "PlaylistOrganizer #" . substr(time(), -random_int(100, 9990));

            $idNew = $spotify->createPlaylist(auth()->user()->spotify_id, $name);

            return redirect()->route('edit-playlist', $idNew);
        } catch (\Exception $e) {
            $this->addError('erroCreatePlaylist', 'Erro ao criar playlist. Tente novamente mais tarde.');
            Log::channel('spotify')->error($e);
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
