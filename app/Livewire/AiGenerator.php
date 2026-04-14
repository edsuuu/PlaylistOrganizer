<?php

namespace App\Livewire;

use App\Models\LikedSong;
use App\Services\GeminiService;
use App\Services\SpotifyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
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

    private GeminiService $gemini;
    private SpotifyService $spotify;

    public function boot(): void
    {
        $this->gemini = new GeminiService();
        $this->spotify = new SpotifyService();
    }

    public function mount(): void
    {
        $this->hasApiKeys = $this->gemini->isConfigured();
    }

    /**
     * Gera a lista de recomendações via Gemini.
     */
    public function generate(): void
    {
        $this->validate([
            'prompt' => 'required|min:3|max:255'
        ], [
            'prompt.required' => 'Diga-me o que você quer ouvir!',
            'prompt.min' => 'Seja um pouco mais descritivo.',
        ]);

        $this->isGenerating = true;
        $this->statusMessage = "Vamos lá! O Gemini está preparando algo incrível para você...";
        $this->tracks = [];

        try {
            $context = '';

            // Se o prompt indicar que quer algo baseado no gosto pessoal ou vibe
            if (preg_match('/(meu gosto|minhas músicas|minhas curtidas|surpreenda|vibe|artista)/i', $this->prompt)) {
                $context = $this->getLikedSongsContext();
            }

            $recommendations = $this->gemini->generatePlaylistRecommendations($this->prompt, $context);

            if (empty($recommendations)) {
                $this->addError('prompt', 'Não consegui gerar recomendações no momento. Tente novamente.');
                $this->isGenerating = false;
                return;
            }

            // Para cada música recomendada, tentamos achar o URI no Spotify
            $ids = [];
            foreach ($recommendations as $item) {
                // Pesquisa rápida no Spotify para pegar metadados reais
                $search = $this->spotify->searchMusics("{$item['name']} {$item['artist']}", 'track', 1);

                if (!empty($search['tracks'])) {
                    $trackData = $search['tracks'][0];
                    $this->tracks[] = [
                        'id' => $trackData['id'],
                        'name' => $trackData['name'],
                        'artist' => $trackData['artist'],
                        'album' => $trackData['album'],
                        'image' => $trackData['image'],
                        'uri' => $trackData['uri'],
                        'is_liked' => false, // Será atualizado em lote
                    ];
                    $ids[] = $trackData['id'];
                }
            }

            // Verifica em lote quais músicas já estão curtidas no Spotify
            if (!empty($ids)) {
                $likedStates = $this->spotify->checkTracksIsLiked($ids);
                foreach ($this->tracks as $index => $track) {
                    $this->tracks[$index]['is_liked'] = $likedStates[$index] ?? false;
                }
            }

            if (empty($this->tracks)) {
                $this->addError('prompt', 'As músicas sugeridas não foram encontradas no Spotify.');
            }

        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro no AiGenerator: " . $e->getMessage());
            $this->addError('prompt', 'Ocorreu um erro inesperado.');
        }

        $this->isGenerating = false;
    }

    /**
     * Alterna o estado de "Curtida" de uma música no Spotify.
     */
    public function toggleLike(int $index): void
    {
        $track = $this->tracks[$index] ?? null;
        if (!$track) return;

        $newState = !$track['is_liked'];
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
        $this->statusMessage = "Buscando uma alternativa similar...";

        try {
            $alternative = $this->gemini->getAlternativeTrack($this->prompt, $this->tracks);

            if ($alternative) {
                $search = $this->spotify->searchMusics("{$alternative['name']} {$alternative['artist']}", 'track', 1);

                if (!empty($search['tracks'])) {
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
            Log::channel('spotify')->error("Erro ao substituir faixa: " . $e->getMessage());
        }

        $this->isGenerating = false;
    }

    /**
     * Cria a playlist no Spotify e adiciona as músicas.
     */
    public function createSpotifyPlaylist()
    {
        if (empty($this->tracks)) return;

        $this->isSaving = true;
        $this->statusMessage = "Criando sua playlist no Spotify...";

        try {
            $user = Auth::user();
            $spotifyId = $user->spotify_id;

            $playlistName = "IA: " . (mb_strlen($this->prompt) > 30 ? mb_substr($this->prompt, 0, 27) . "..." : $this->prompt);

            $playlistId = $this->spotify->createPlaylist($spotifyId, $playlistName);

            if ($playlistId) {
                $uris = array_column($this->tracks, 'uri');
                $this->spotify->addMusicsInPlaylist($playlistId, $uris);

                $this->dispatch('refreshPlaylistsUser');

                Session::flash('message', 'Playlist criada com sucesso!');
                return Redirect::route('edit-playlist', ['id' => $playlistId]);
            } else {
                throw new \Exception("A API do Spotify não retornou um ID de playlist válido.");
            }
        } catch (\Exception $e) {
            Log::channel('spotify')->error("Erro ao salvar playlist: " . $e->getMessage());
            $this->addError('save', 'Erro ao salvar playlist no Spotify.');
        }

        $this->isSaving = false;
    }

    public function tryAgain(): void
    {
        $this->tracks = [];
        $this->prompt = '';
    }

    private function getLikedSongsContext(): string
    {
        $songs = LikedSong::query()
            ->where('user_id', Auth::id())
            ->inRandomOrder()
            ->limit(20)
            ->get(['name', 'artist']);

        if ($songs->isEmpty()) return '';

        return $songs->map(fn($s) => "{$s->name} - {$s->artist}")->implode(', ');
    }

    public function render()
    {
        return view('livewire.ai-generator');
    }
}
