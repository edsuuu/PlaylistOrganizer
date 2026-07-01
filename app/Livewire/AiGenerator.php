<?php

namespace App\Livewire;

use App\Models\LikedSong;
use App\Services\GeminiService;
use App\Services\SpotifyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class AiGenerator extends Component
{
    public string $prompt = '';

    public array $tracks = [];

    public bool $isGenerating = false;

    public bool $isSaving = false;

    public bool $hasApiKeys = false;

    public string $statusMessage = '';

    /** Busca da música de referência (semente). */
    public string $reference = '';

    public array $referenceResults = [];

    public ?array $selectedReference = null;

    /** Motor de recomendação: 'playlists' (Spotify, sem IA) ou 'ai' (Gemini). */
    public string $engine = 'playlists';

    /** Alvo de faixas na playlist final e buffer pedido ao Gemini (cobre misses/dedup/curtidas removidas). */
    private const TARGET = 30;

    private const BUFFER = 40;

    private GeminiService $gemini;

    private SpotifyService $spotify;

    public function boot(): void
    {
        $this->gemini = new GeminiService;
        $this->spotify = new SpotifyService;
    }

    public function mount(): void
    {
        $this->hasApiKeys = $this->gemini->isConfigured();
    }

    /**
     * Hook do Livewire: busca no Spotify enquanto o usuário digita a referência.
     */
    public function updatedReference(): void
    {
        $this->searchReference();
    }

    /**
     * Busca faixas no Spotify para escolher a música de referência (semente).
     */
    public function searchReference(): void
    {
        $term = trim($this->reference);

        if (mb_strlen($term) < 2) {
            $this->referenceResults = [];

            return;
        }

        $search = $this->spotify->searchMusics($term, 'track', 5);
        $this->referenceResults = $search['tracks'] ?? [];
    }

    /**
     * Define a música de referência a partir dos resultados da busca.
     */
    public function selectReference(int $index): void
    {
        $track = $this->referenceResults[$index] ?? null;
        if (! $track) {
            return;
        }

        $this->selectedReference = [
            'id' => $track['id'],
            'name' => $track['name'],
            'artist' => $track['artist'],
            'image' => $track['image'] ?? null,
            'uri' => $track['uri'] ?? null,
        ];

        $this->reference = '';
        $this->referenceResults = [];
    }

    public function clearReference(): void
    {
        $this->selectedReference = null;
        $this->reference = '';
        $this->referenceResults = [];
    }

    /**
     * Gera a lista de recomendações via Gemini.
     */
    public function generate(): void
    {
        $prompt = trim($this->prompt);

        // Precisa de pelo menos um: música de referência OU descrição da vibe.
        if (! $this->selectedReference && $prompt === '') {
            $this->addError('prompt', 'Escolha uma música de referência ou descreva a vibe.');

            return;
        }

        if ($prompt !== '' && mb_strlen($prompt) > 255) {
            $this->addError('prompt', 'Descrição muito longa (máx. 255 caracteres).');

            return;
        }

        // A geração (Gemini + ~30 buscas no Spotify) pode passar dos 30s padrão do php artisan serve.
        set_time_limit(120);

        $this->isGenerating = true;
        $this->statusMessage = 'Estamos montando sua playlist...';
        $this->tracks = [];

        try {
            $candidates = $this->engine === 'ai'
                ? $this->geminiCandidates($prompt)
                : $this->playlistCandidates($prompt);

            if (empty($candidates)) {
                $this->addError('prompt', $this->engine === 'ai'
                    ? 'Não consegui gerar recomendações no momento. Tente novamente.'
                    : 'Não encontrei playlists para essa vibe/referência. Tente outro termo.');
                $this->isGenerating = false;

                return;
            }

            // Dedup, descarta as curtidas e limita o total final.
            $this->finalizeTracks($candidates);

            if (empty($this->tracks)) {
                $this->addError('prompt', 'Não encontrei músicas novas fora das suas curtidas. Tente outra vibe ou referência.');
            }

        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro no AiGenerator: '.$e->getMessage());
            $this->addError('prompt', 'Ocorreu um erro inesperado.');
        }

        $this->isGenerating = false;
    }

    /**
     * Motor de IA (Gemini): gera nomes e resolve cada faixa no Spotify.
     * Retorna candidatos crus (sem filtro de curtidas).
     */
    private function geminiCandidates(string $prompt): array
    {
        // Curtidas como contexto para o Gemini já evitá-las.
        $context = $this->getLikedSongsContext();

        $referenceTrack = $this->selectedReference
            ? "{$this->selectedReference['name']} - {$this->selectedReference['artist']}"
            : '';

        $recommendations = $this->gemini->generateSimilarPlaylist($prompt, $referenceTrack, self::BUFFER, $context);

        $candidates = [];
        foreach ($recommendations as $item) {
            if (empty($item['name']) || empty($item['artist'])) {
                continue;
            }

            $search = $this->spotify->searchMusics("{$item['name']} {$item['artist']}", 'track', 1);
            if (! empty($search['tracks'])) {
                $candidates[] = $search['tracks'][0];
            }
        }

        return $candidates;
    }

    /**
     * Motor sem IA: junta faixas de playlists públicas do Spotify ligadas à
     * referência e/ou à vibe. Retorna candidatos crus (já embaralhados).
     */
    private function playlistCandidates(string $prompt): array
    {
        $playlistIds = [];
        foreach ($this->buildPlaylistQueries($prompt) as $query) {
            foreach ($this->spotify->searchPlaylists($query, 3) as $playlist) {
                $playlistIds[$playlist['id']] = true;
                if (count($playlistIds) >= 4) {
                    break 2;
                }
            }
        }

        $pool = [];
        foreach (array_keys($playlistIds) as $id) {
            foreach ($this->spotify->getPlaylistTracksRaw($id, 100) as $track) {
                $pool[] = $track;
            }
        }

        shuffle($pool);

        // Limita o pool antes do filtro de curtidas (mantém poucas chamadas).
        return array_slice($pool, 0, 80);
    }

    /**
     * Monta os termos de busca de playlists a partir da vibe e/ou da referência.
     *
     * @return array<int, string>
     */
    private function buildPlaylistQueries(string $prompt): array
    {
        $queries = [];

        $vibe = trim($prompt);
        if ($vibe !== '') {
            $queries[] = $vibe;
        }

        if ($this->selectedReference) {
            $artist = $this->selectedReference['artist'];
            $queries[] = "This Is {$artist}";
            $queries[] = "{$artist} mix";

            $info = $this->spotify->getArtistByName($artist);
            foreach (array_slice($info['genres'] ?? [], 0, 2) as $genre) {
                $queries[] = $genre;
            }
        }

        return array_slice(array_values(array_unique($queries)), 0, 4);
    }

    /**
     * Dedup por id, remove a referência e as curtidas, e limita o total.
     */
    private function finalizeTracks(array $candidates, int $max = 40): void
    {
        // Curtidas reais do Spotify por título|artista (pega variações com id diferente).
        $likedKeys = $this->spotify->getLikedTrackKeys();

        $seen = [];
        if ($this->selectedReference) {
            $seen[$this->selectedReference['id']] = true; // nunca incluir a própria referência
        }

        $unique = [];
        foreach ($candidates as $candidate) {
            if (empty($candidate['id']) || isset($seen[$candidate['id']])) {
                continue;
            }
            $seen[$candidate['id']] = true;

            // Descarta a mesma música já curtida, mesmo que venha com outro id.
            $key = $this->spotify->likedKey($candidate['name'] ?? '', $candidate['artist'] ?? '');
            if (isset($likedKeys[$key])) {
                continue;
            }

            $unique[] = $candidate;
        }

        $kept = [];
        foreach (array_chunk($unique, 50) as $chunk) {
            $chunk = array_values($chunk);
            $likedStates = $this->spotify->checkTracksIsLiked(array_column($chunk, 'id'));

            foreach ($chunk as $index => $candidate) {
                if (! empty($likedStates[$index])) {
                    continue; // já está nas curtidas → descarta
                }

                $kept[] = [
                    'id' => $candidate['id'],
                    'name' => $candidate['name'],
                    'artist' => $candidate['artist'],
                    'album' => $candidate['album'] ?? '',
                    'image' => $candidate['image'] ?? null,
                    'uri' => $candidate['uri'],
                    'is_liked' => false,
                ];

                if (count($kept) >= $max) {
                    break 2;
                }
            }
        }

        $this->tracks = $kept;
    }

    /**
     * Alterna o estado de "Curtida" de uma música no Spotify.
     */
    public function toggleLike(int $index): void
    {
        $track = $this->tracks[$index] ?? null;
        if (! $track) {
            return;
        }

        $newState = ! $track['is_liked'];
        $success = $this->spotify->toggleLikeTrack($track['id'], $newState);

        if ($success) {
            $this->tracks[$index]['is_liked'] = $newState;
            $this->dispatch('refreshLikedSongs'); // Opcional, se houver ouvintes
        }
    }

    public function play(string $uri): void
    {
        $this->spotify->playTrack($uri);
        $this->dispatch('playbackUpdated');
    }

    /**
     * Define o prompt com a sugestão sem disparar a geração automaticamente.
     */
    public function applySuggestion(string $prompt): void
    {
        $this->prompt = $prompt;
    }

    /**
     * Remove uma música da lista.
     */
    public function removeTrack(int $index): void
    {
        unset($this->tracks[$index]);
        $this->tracks = array_values($this->tracks);
    }

    /**
     * Substitui uma música por outra do mesmo contexto.
     */
    public function replaceTrack(int $index): void
    {
        $this->isGenerating = true;
        $this->statusMessage = 'Buscando uma alternativa similar...';

        try {
            $alternative = $this->gemini->getAlternativeTrack($this->prompt, $this->tracks);

            if ($alternative) {
                $search = $this->spotify->searchMusics("{$alternative['name']} {$alternative['artist']}", 'track', 1);

                if (! empty($search['tracks'])) {
                    $trackData = $search['tracks'][0];
                    $this->tracks[$index] = [
                        'id' => $trackData['id'],
                        'name' => $trackData['name'],
                        'artist' => $trackData['artist'],
                        'album' => $trackData['album'],
                        'image' => $trackData['image'],
                        'uri' => $trackData['uri'],
                        'is_liked' => $this->spotify->checkTracksIsLiked([$trackData['id']])[0] ?? false,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao substituir faixa: '.$e->getMessage());
        }

        $this->isGenerating = false;
    }

    /**
     * Cria a playlist no Spotify e adiciona as músicas.
     */
    public function createSpotifyPlaylist()
    {
        if (empty($this->tracks)) {
            return;
        }

        $this->isSaving = true;
        $this->statusMessage = 'Criando sua playlist no Spotify...';

        try {
            $user = Auth::user();
            $spotifyId = $user->spotify_id;

            [$playlistName, $playlistDescription] = $this->buildPlaylistMeta();

            $playlistId = $this->spotify->createPlaylist($spotifyId, $playlistName, $playlistDescription);

            if ($playlistId) {
                $uris = array_column($this->tracks, 'uri');
                $this->spotify->addMusicsInPlaylist($playlistId, $uris);

                $this->dispatch('refreshPlaylistsUser');

                Session::flash('message', 'Playlist criada com sucesso!');

                return Redirect::route('edit-playlist', ['id' => $playlistId]);
            } else {
                throw new \Exception('A API do Spotify não retornou um ID de playlist válido.');
            }
        } catch (\Exception $e) {
            Log::channel('spotify')->error('Erro ao salvar playlist: '.$e->getMessage());
            $this->addError('save', 'Erro ao salvar playlist no Spotify.');
        }

        $this->isSaving = false;
    }

    public function tryAgain(): void
    {
        $this->tracks = [];
        $this->prompt = '';
        $this->clearReference();
    }

    /**
     * Monta nome (curto, ≤30 chars) e descrição (concreta) da playlist.
     *
     * @return array{0:string,1:string} [name, description]
     */
    private function buildPlaylistMeta(): array
    {
        $vibe = trim($this->prompt);

        if ($this->selectedReference) {
            $ref = $this->selectedReference;
            $name = $this->trimText("Vibe de {$ref['name']}", 30);

            $vibePart = $vibe !== '' ? ' · '.$this->trimText($vibe, 80) : '';
            $description = "Faixas na pegada de \"{$ref['name']} – {$ref['artist']}\"".$vibePart
                .'. Criada no PlaylistOrganizer.';
        } else {
            $name = $this->trimText("Vibe: {$vibe}", 30);
            $description = 'Playlist a partir de "'.$this->trimText($vibe, 100).'". Criada no PlaylistOrganizer.';
        }

        return [$name, $description];
    }

    private function trimText(string $text, int $max): string
    {
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 1).'…' : $text;
    }

    private function getLikedSongsContext(): string
    {
        $songs = LikedSong::query()
            ->where('user_id', Auth::id())
            ->inRandomOrder()
            ->limit(50)
            ->get(['name', 'artist']);

        if ($songs->isEmpty()) {
            return '';
        }

        return $songs->map(fn ($s) => "{$s->name} - {$s->artist}")->implode(', ');
    }

    public function render()
    {
        return view('livewire.ai-generator');
    }
}
