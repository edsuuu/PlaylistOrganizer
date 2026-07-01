<div class="min-h-[80vh] flex flex-col items-center justify-center p-4">
    
    @if(empty($tracks) && !$isGenerating)
        <!-- Initial State: Central Input -->
        <div class="w-full max-w-2xl text-center space-y-8 animate-fade-in">
            <div class="space-y-4">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-tr from-purple-600 to-pink-500 shadow-xl shadow-purple-500/20 mb-4 ring-4 ring-white dark:ring-zinc-800">
                    <flux:icon.sparkles class="w-10 h-10 text-white fill-current" />
                </div>
                <h1 class="text-4xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Descobrir Músicas Novas</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-lg">Informe uma música de referência, descreva a vibe — ou os dois. Montamos uma playlist na mesma pegada.</p>
                <div class="flex items-center justify-center gap-2 pt-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-300 text-xs font-semibold">
                        <flux:icon.musical-note class="w-3.5 h-3.5" /> 30+ músicas
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs font-semibold">
                        <flux:icon.heart class="w-3.5 h-3.5" /> sem repetir suas curtidas
                    </span>
                </div>
            </div>

            @php $blocked = $engine === 'ai' && ! $hasApiKeys; @endphp
            <form wire:submit.prevent="generate" class="space-y-4">
                <!-- Seletor de motor -->
                <div class="flex flex-col items-center gap-2">
                    <div class="inline-flex p-1 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80">
                        <button type="button" wire:click="$set('engine', 'playlists')"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all cursor-pointer flex items-center gap-1.5 {{ $engine === 'playlists' ? 'bg-white dark:bg-zinc-900 text-purple-600 shadow' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}">
                            <flux:icon.musical-note class="w-4 h-4" /> Playlists do Spotify
                        </button>
                        <button type="button" wire:click="$set('engine', 'ai')"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all cursor-pointer flex items-center gap-1.5 {{ $engine === 'ai' ? 'bg-white dark:bg-zinc-900 text-purple-600 shadow' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}">
                            <flux:icon.sparkles class="w-4 h-4" /> IA (Gemini)
                        </button>
                    </div>
                    <p class="text-xs text-zinc-400 text-center">{{ $engine === 'ai' ? 'Gera nomes com o Gemini e busca cada faixa (sujeito a limites de uso da IA).' : 'Usa playlists públicas do Spotify — rápido e sem limites de IA.' }}</p>
                </div>

                @if($engine === 'ai' && ! $hasApiKeys)
                    <div class="p-4 bg-red-100 dark:bg-red-900/40 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm flex items-center gap-3">
                        <flux:icon.exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                        <p>A chave da API do Google (Gemini) não foi configurada no seu arquivo .env. Por favor, configure GOOGLE_GEMINI_KEY.</p>
                    </div>
                @endif

                <div class="flex flex-col gap-6 text-left">
                    <!-- Passo 1: Música de referência (opcional) -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold flex-shrink-0">1</span>
                            <label class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Música de referência <span class="text-zinc-400 font-normal">· opcional</span></label>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 pl-8">Escolha uma faixa base — trazemos músicas com a mesma batida e energia.</p>

                        <div class="relative pl-8">
                            @if($selectedReference)
                                <div class="flex items-center gap-3 px-4 py-3 bg-purple-50 dark:bg-purple-900/20 border-2 border-purple-300 dark:border-purple-700/60 rounded-2xl">
                                    @if($selectedReference['image'])
                                        <img src="{{ $selectedReference['image'] }}" class="w-11 h-11 rounded-lg shadow flex-shrink-0">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-purple-600 flex items-center gap-1">
                                            <flux:icon.check-circle class="w-3.5 h-3.5" /> Referência escolhida
                                        </p>
                                        <p class="font-bold text-zinc-900 dark:text-white truncate">{{ $selectedReference['name'] }}</p>
                                        <p class="text-xs text-zinc-500 truncate">{{ $selectedReference['artist'] }}</p>
                                    </div>
                                    <button type="button" wire:click="clearReference" title="Trocar / remover referência" class="p-2 text-zinc-400 hover:text-red-500 transition-colors cursor-pointer">
                                        <flux:icon.x-mark class="w-5 h-5" />
                                    </button>
                                </div>
                            @else
                                <div class="relative">
                                    <flux:icon.magnifying-glass class="w-5 h-5 text-zinc-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    <input
                                        type="text"
                                        wire:model.live.debounce.500ms="reference"
                                        @disabled($blocked)
                                        placeholder="Buscar faixa no Spotify (ex.: nome da música - artista)"
                                        class="w-full pl-12 pr-12 py-3.5 bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-md focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all outline-none dark:text-white {{ $blocked ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    >
                                    <flux:icon.arrow-path wire:loading wire:target="reference" class="w-5 h-5 text-purple-500 animate-spin absolute right-4 top-1/2 -translate-y-1/2" />
                                </div>

                                @if(!empty($referenceResults))
                                    <div class="absolute z-20 mt-2 w-full max-h-72 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl divide-y divide-zinc-100 dark:divide-zinc-800">
                                        @foreach($referenceResults as $index => $result)
                                            <button type="button" wire:click="selectReference({{ $index }})" class="w-full flex items-center gap-3 p-3 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors text-left cursor-pointer">
                                                <img src="{{ $result['image'] }}" class="w-10 h-10 rounded-lg shadow flex-shrink-0">
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $result['name'] }}</p>
                                                    <p class="text-xs text-zinc-500 truncate">{{ $result['artist'] }}</p>
                                                </div>
                                                <flux:icon.plus class="w-5 h-5 text-purple-500 flex-shrink-0" />
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif(strlen(trim($reference)) >= 2)
                                    <p class="text-xs text-zinc-400 mt-2 pl-2">Nenhuma faixa encontrada. Tente "música - artista".</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Passo 2: Vibe (obrigatória apenas se não houver referência) -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold flex-shrink-0">2</span>
                            <label class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                Descreva a vibe
                                @if($selectedReference)
                                    <span class="text-zinc-400 font-normal">· opcional</span>
                                @else
                                    <span class="text-pink-500">*</span>
                                @endif
                            </label>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 pl-8">Estilo, clima ou ocasião que você quer ouvir.{{ $selectedReference ? ' Com uma referência escolhida, é opcional.' : '' }}</p>

                        <div class="relative pl-8">
                            <textarea
                                rows="3"
                                wire:model="prompt"
                                @disabled($blocked)
                                placeholder="{{ ! $blocked ? 'Ex.: pancadão com batida forte pra treino' : 'Configuração pendente...' }}"
                                class="w-full px-6 py-4 bg-white dark:bg-zinc-900 border-2 {{ $blocked ? 'border-red-200 dark:border-red-900/50 cursor-not-allowed opacity-50' : 'border-zinc-200 dark:border-zinc-800' }} rounded-2xl shadow-md focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all text-lg outline-none dark:text-white resize-none"
                            ></textarea>
                            @error('prompt')
                                <span class="text-red-500 text-xs mt-2 block pl-2">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit"
                        @disabled($blocked)
                        wire:loading.attr="disabled"
                        wire:target="generate"
                        class="w-full h-[60px] px-8 {{ ! $blocked ? 'bg-gradient-to-r from-purple-600 to-pink-500 hover:opacity-90 shadow-purple-600/30 cursor-pointer active:scale-95' : 'bg-zinc-400 dark:bg-zinc-700 cursor-not-allowed' }} text-white font-bold rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 disabled:opacity-60"
                    >
                        <flux:icon.sparkles wire:loading.remove wire:target="generate" class="w-5 h-5 fill-current" />
                        <span wire:loading.remove wire:target="generate">{{ $selectedReference ? 'Gerar 30+ na mesma vibe' : 'Gerar playlist' }}</span>
                        <span wire:loading wire:target="generate" class="flex items-center gap-2">
                             <flux:icon.arrow-path class="w-5 h-5 animate-spin" />
                             Gerando recomendações...
                        </span>
                    </button>
                </div>
            </form>

            <div class="space-y-3">
                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400">Sem ideia? Toque numa vibe</p>
                <div class="flex flex-wrap justify-center gap-2 text-sm overflow-x-auto pb-2 px-2">
                    @php
                        $suggestions = [
                            'Pancadão pra treino',
                            'Pagode pra resenha',
                            'Sertanejo pra viagem',
                            'Trap nacional',
                            'Foco / concentração',
                            'Me surpreenda com o meu gosto',
                        ];
                    @endphp
                    @foreach($suggestions as $suggestion)
                        <button
                            type="button"
                            @disabled($blocked)
                            wire:click="applySuggestion('{{ $suggestion }}')"
                            class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 {{ ! $blocked ? 'hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:text-purple-600 cursor-pointer active:scale-95' : 'cursor-not-allowed' }} text-zinc-600 dark:text-zinc-400 rounded-full transition-all whitespace-nowrap text-xs font-medium"
                        >
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Full Screen Premium Loading Overlay (dirigido por wire:loading) -->
    <div
        wire:loading.flex
        wire:target="generate, createSpotifyPlaylist"
        x-data="{
            step: 0,
            messages: [
                'Garimpando as melhores faixas para você...',
                'Analisando o clima da sua solicitação...',
                'Explorando milhares de possibilidades musicais...',
                'Quase pronto! Organizando as faixas perfeitas...',
                'Aguarde mais um pouco, a mágica está acontecendo...'
            ],
            init() {
                setInterval(() => {
                    this.step = (this.step + 1) % this.messages.length;
                }, 3500);

                // Trava o scroll do body enquanto o overlay (wire:loading) estiver visível.
                const lockScroll = () => {
                    const visible = getComputedStyle(this.$el).display !== 'none';
                    document.body.style.overflow = visible ? 'hidden' : '';
                };
                new MutationObserver(lockScroll).observe(this.$el, { attributes: true, attributeFilter: ['style'] });
                lockScroll();
            }
        }"
        class="fixed inset-0 z-50 items-center justify-center backdrop-blur-2xl bg-black/70"
    >
            <div class="text-center space-y-12 max-w-lg px-6">
                <!-- Premium Glowing Animated Loader -->
                <div class="relative w-48 h-48 mx-auto">
                    <!-- Outer Glows -->
                    <div class="absolute inset-0 bg-purple-600/40 rounded-full blur-[60px] animate-pulse"></div>
                    <div class="absolute inset-[-20px] border border-white/5 rounded-full animate-[spin_10s_linear_infinite]"></div>
                    <div class="absolute inset-[-40px] border border-white/5 rounded-full animate-[spin_15s_linear_infinite_reverse]"></div>
                    
                    <div class="relative w-48 h-48 flex items-center justify-center bg-zinc-900/90 rounded-full border border-zinc-700/50 shadow-2xl overflow-hidden ring-8 ring-white/5">
                        <!-- Moving Gradient Background -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-purple-600/30 to-pink-500/30 animate-pulse"></div>
                        
                        <!-- Spinning Hero Icon -->
                        <flux:icon.sparkles class="w-24 h-24 text-white fill-current animate-[spin_4s_linear_infinite]" />
                    </div>
                </div>

                <!-- Animated Messaging -->
                <div class="space-y-6">
                    <div class="min-h-[80px] flex items-center justify-center">
                        <h2 
                            class="text-3xl font-black text-white tracking-tight leading-tight"
                            x-text="messages[step]"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                        ></h2>
                    </div>

                    <!-- Step Progress Dots -->
                    <div class="flex justify-center gap-2">
                        <template x-for="(m, i) in messages">
                            <div 
                                class="h-1.5 rounded-full transition-all duration-700"
                                :class="step === i ? 'w-10 bg-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.5)]' : 'w-2 bg-zinc-700'"
                            ></div>
                        </template>
                    </div>

                    <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest animate-pulse">Montando sua playlist</p>
                </div>
            </div>
        </div>

    @if(! empty($tracks))
        <!-- Results State: Track List -->
        <div class="w-full max-w-7xl space-y-8 animate-fade-in-up">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-zinc-200 dark:border-zinc-800">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-widest text-purple-600">Resultado para</span>
                    <h2 class="text-3xl font-extrabold text-zinc-900 dark:text-white">"{{ $prompt }}"</h2>
                    @if($selectedReference)
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                            <flux:icon.musical-note class="w-4 h-4 text-purple-500" />
                            na vibe de <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $selectedReference['name'] }} — {{ $selectedReference['artist'] }}</span>
                        </p>
                    @endif
                    <p class="text-xs text-zinc-400">{{ count($tracks) }} faixas</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button 
                        wire:confirm="Isso limpará os resultados atuais. Deseja continuar?" 
                        wire:click="tryAgain" 
                        class="px-6 py-3 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold rounded-xl transition-all active:scale-95 cursor-pointer"
                    >
                        Tentar Novamente
                    </button>
                    <button 
                        wire:click="createSpotifyPlaylist" 
                        @disabled($isSaving)
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold rounded-xl shadow-xl shadow-purple-500/30 hover:scale-105 transition-all active:scale-95 flex items-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <flux:icon.musical-note wire:loading.remove wire:target="createSpotifyPlaylist" class="w-5 h-5" />
                        <flux:icon.arrow-path wire:loading wire:target="createSpotifyPlaylist" class="w-5 h-5 animate-spin" />
                        <span wire:loading.remove wire:target="createSpotifyPlaylist">Gerar Playlist no Spotify</span>
                        <span wire:loading wire:target="createSpotifyPlaylist">Sincronizando...</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($tracks as $index => $track)
                    <div 
                        class="group flex items-center gap-4 bg-white dark:bg-zinc-900/40 p-3 pr-5 rounded-2xl border border-zinc-200 dark:border-zinc-800/60 hover:shadow-xl transition-all hover:bg-zinc-50 dark:hover:bg-zinc-800/80"
                    >
                        <!-- Image with Play Hover -->
                        <div class="relative w-14 h-14 flex-shrink-0 cursor-pointer" wire:click="play('{{ $track['uri'] }}')">
                            <img src="{{ $track['image'] }}" class="w-full h-full rounded-lg shadow-md group-hover:scale-105 transition-transform">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                <flux:icon.play class="w-6 h-6 text-white fill-current" />
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-zinc-900 dark:text-zinc-100 truncate cursor-pointer" wire:click="play('{{ $track['uri'] }}')">{{ $track['name'] }}</h4>
                            <p class="text-xs text-zinc-500 truncate">{{ $track['artist'] }}</p>
                        </div>

                        <div class="flex items-center gap-1">
                            <!-- Like Button -->
                            <button 
                                wire:click="toggleLike({{ $index }})" 
                                title="{{ $track['is_liked'] ? 'Remover dos curtidas' : 'Curtir no Spotify' }}" 
                                class="p-2 transition-colors cursor-pointer {{ $track['is_liked'] ? 'text-green-500 hover:text-green-600' : 'text-zinc-400 hover:text-white' }}"
                            >
                                @if($track['is_liked'])
                                    <flux:icon.heart class="w-5 h-5 fill-current" />
                                @else
                                    <flux:icon.heart class="w-5 h-5" />
                                @endif
                            </button>

                            <!-- Controls -->
                            <div class="flex gap-1 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="replaceTrack({{ $index }})" title="Obter alternativa similar" class="p-2 hover:bg-purple-100 dark:hover:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-lg transition-colors cursor-pointer">
                                    <flux:icon.arrow-path wire:loading.remove wire:target="replaceTrack({{ $index }})" class="w-5 h-5" />
                                    <flux:icon.arrow-path wire:loading wire:target="replaceTrack({{ $index }})" class="w-5 h-5 animate-spin" />
                                </button>
                                <button wire:click="removeTrack({{ $index }})" title="Remover da lista" class="p-2 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg transition-colors cursor-pointer">
                                    <flux:icon.x-mark class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-fade-in { animation: opacity 0.5s ease-in; }
    </style>
</div>
