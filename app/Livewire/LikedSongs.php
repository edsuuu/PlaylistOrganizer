<?php

namespace App\Livewire;

use App\Models\LikedSong;
use App\Services\SpotifyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LikedSongs extends Component
{
    private SpotifyService $spotify;
    
    public int $totalSpotify = 0;
    public int $totalDb = 0;
    public bool $isSyncing = false;
    public bool $loadFromApi = false;
    public array $apiTracks = [];
    public string $search = '';
    public int $perPage = 50;

    public array $duplicates = [];
    public bool $isChecking = false;
    public array $duplicatePositions = [];

    protected $queryString = ['search'];

    public function __construct()
    {
        $this->spotify = new SpotifyService();
    }

    public function updatedSearch(): void
    {
        $this->perPage = 50;
    }

    public function loadMore(): void
    {
        $this->perPage += 50;
    }

    public function mount(): void
    {
        $user = Auth::user();
        
        // Busca o total direto do Spotify (agora pegando 50 para o primeiro render)
        $spotifyData = $this->spotify->getFavoriteMusics(0, 50);
        $this->totalSpotify = $spotifyData['total'] ?? 0;
        
        // Busca o total no banco
        $this->totalDb = LikedSong::query()->where('user_id', $user->id)->count();

        if ($this->totalSpotify > $this->totalDb) {
            $this->loadFromApi = true;
            $this->apiTracks = $spotifyData['tracks'] ?? [];
            // Inicia sincronização em "segundo plano" via wire:init ou evento
        } else {
            $this->loadFromApi = false;
        }
    }

    /**
     * Inicia a sincronização das músicas curtidas.
     * Chamado via wire:init no front-end.
     */
    public function syncLikedSongs(): void
    {
        if ($this->totalDb >= $this->totalSpotify) {
            return;
        }

        $this->isSyncing = true;
        $user = Auth::user();
        $offset = 0;
        $limit = 50;

        try {
            do {
                $response = $this->spotify->getFavoriteMusics($offset, $limit);
                $tracks = $response['tracks'] ?? [];

                foreach ($tracks as $track) {
                    LikedSong::query()->updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'spotify_id' => $track['id'],
                        ],
                        [
                            'name' => $track['name'],
                            'artist' => $track['artist'],
                            'album' => $track['album'],
                            'image' => $track['image'],
                            'duration_ms' => $track['duration_ms'],
                            'preview_url' => $track['preview_url'],
                            'uri' => $track['uri'],
                            'added_at' => $track['added_at'] ?? Carbon::now(),
                        ]
                    );
                }

                $offset += $limit;
                $this->totalDb = LikedSong::query()->where('user_id', $user->id)->count();
                
                // Após o primeiro batch, já podemos carregar do Banco
                if ($this->loadFromApi && $this->totalDb > 0) {
                    $this->loadFromApi = false;
                }

                // Força um refresh no front se necessário
                $this->dispatch('syncProgress', count: $this->totalDb);

            } while (!empty($response['nextUrl']) && $offset < $this->totalSpotify);

            $this->isSyncing = false;
            $this->loadFromApi = false; // Após sincronizar, carrega do banco
            $this->totalDb = LikedSong::query()->where('user_id', $user->id)->count();

        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro na sincronização: " . $e->getMessage());
            $this->isSyncing = false;
        }
    }

    /**
     * Identifica músicas duplicadas nas curtidas por ID ou Nome.
     */
    public function checkDuplicates(): void
    {
        $this->isChecking = true;
        $this->duplicates = [];
        
        try {
            // Buscamos as músicas que o usuário tem no banco (Sincronizadas)
            $dbTracks = LikedSong::query()
                ->where('user_id', Auth::id())
                ->get();
                
            $seenIds = [];
            $seenNames = [];

            foreach ($dbTracks as $index => $track) {
                $id = $track->spotify_id;
                $name = mb_strtolower((string) $track->name);
                $artist = mb_strtolower((string) $track->artist);
                $compositeKey = "{$name}|{$artist}";
                
                $isDuplicate = false;
                $reason = '';

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
                        'name' => $track->name,
                        'artist' => $track->artist,
                        'image' => $track->image,
                        'uri' => $track->uri,
                        'position' => $track->id, // No banco usamos o ID da tabela
                        'reason' => $reason
                    ];
                }

                if ($id) $seenIds[$id] = true;
                if ($compositeKey) $seenNames[$compositeKey] = true;
            }
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao validar duplicatas em curtidas: " . $e->getMessage());
        }

        $this->isChecking = false;
        $this->dispatch('show-duplicates');
    }

    /**
     * Remove as duplicatas selecionadas (Unlikes no Spotify e delete no banco).
     */
    public function removeSelectedDuplicates(): void
    {
        if (empty($this->duplicatePositions)) return;

        try {
            $tracksToRemove = LikedSong::query()
                ->whereIn('id', $this->duplicatePositions)
                ->get();

            $spotifyIds = $tracksToRemove->pluck('spotify_id')->filter()->toArray();

            if (!empty($spotifyIds)) {
                // Remove do Spotify (Unlike)
                $this->spotify->removeFavoriteTracks($spotifyIds);
            }

            foreach ($tracksToRemove as $track) {
                /** @var LikedSong $track */
                $track->delete();
            }

            // Atualiza os totais
            $user = Auth::user();
            $this->totalDb = LikedSong::query()->where('user_id', $user->id)->count();
            // Estimativa: subtraímos do totalSpotify também para manter consistência imediata
            $this->totalSpotify = max(0, $this->totalSpotify - count($spotifyIds));
            
            $this->duplicates = [];
            $this->duplicatePositions = [];
            Session::flash('message', 'Duplicadas removidas da sua biblioteca!');
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao remover duplicatas de curtidas: " . $e->getMessage());
        }
    }

    public function play(string $uri): void
    {
        $this->spotify->playTrack($uri);
        $this->dispatch('playbackUpdated');
    }

    public function render()
    {
        $user = Auth::user();

        // Se estiver carregando da API (primeiro acesso ou desatualizado)
        // e ainda não terminou a sync, mostramos o que veio da API
        if ($this->loadFromApi) {
            $tracks = $this->apiTracks;
        } else {
            $tracks = LikedSong::query()
                ->where('user_id', $user->id)
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('artist', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('added_at', 'desc')
                ->limit($this->perPage)
                ->get();
        }

        return view('livewire.liked-songs', [
            'tracks' => $tracks
        ]);
    }
}
