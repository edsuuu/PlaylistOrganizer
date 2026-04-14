<div class="min-h-[80vh] flex flex-col items-center justify-center p-4">
    
    @if(empty($tracks) && !$isGenerating)
        <!-- Initial State: Central Input -->
        <div class="w-full max-w-2xl text-center space-y-8 animate-fade-in">
            <div class="space-y-4">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-tr from-purple-600 to-pink-500 shadow-xl shadow-purple-500/20 mb-4 ring-4 ring-white dark:ring-zinc-800">
                    <flux:icon.sparkles class="w-10 h-10 text-white fill-current" />
                </div>
                <h1 class="text-4xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Gerar Playlist com IA</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-lg">Descreva o que você quer ouvir e deixe o Gemini fazer a mágica.</p>
            </div>

            <form wire:submit.prevent="generate" class="space-y-4">
                @if(!$hasApiKeys)
                    <div class="p-4 bg-red-100 dark:bg-red-900/40 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm flex items-center gap-3">
                        <flux:icon.exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                        <p>A chave da API do Google (Gemini) não foi configurada no seu arquivo .env. Por favor, configure GOOGLE_GEMINI_KEY.</p>
                    </div>
                @endif

                <div class="flex flex-col gap-4">
                    <div class="flex-1 relative">
                        <textarea 
                            rows="3"
                            wire:model="prompt"
                            @disabled(!$hasApiKeys)
                            placeholder="{{ $hasApiKeys ? 'Descreva o que você quer ouvir...' : 'Configuração pendente...' }}" 
                            class="w-full px-6 py-4 bg-white dark:bg-zinc-900 border-2 {{ !$hasApiKeys ? 'border-red-200 dark:border-red-900/50 cursor-not-allowed opacity-50' : 'border-zinc-200 dark:border-zinc-800' }} rounded-2xl shadow-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all text-lg outline-none dark:text-white resize-none"
                        ></textarea>
                        @error('prompt')
                            <span class="text-red-500 text-xs mt-2 block pl-2 text-left">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <button type="submit" 
                        @disabled(!$hasApiKeys || $isGenerating) 
                        class="w-full h-[60px] px-8 {{ $hasApiKeys ? 'bg-purple-600 hover:bg-purple-700 shadow-purple-600/30 cursor-pointer active:scale-95' : 'bg-zinc-400 dark:bg-zinc-700 cursor-not-allowed' }} text-white font-bold rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="generate">Gerar Playlist com IA</span>
                        <span wire:loading wire:target="generate" class="flex items-center gap-2">
                             <flux:icon.arrow-path class="w-5 h-5 animate-spin" />
                             Gerando Recomendações...
                        </span>
                        <flux:icon.paper-airplane wire:loading.remove wire:target="generate" class="w-5 h-5 -rotate-45" />
                    </button>
                </div>
            </form>

            <div class="flex flex-wrap justify-center gap-2 text-sm overflow-x-auto pb-4 px-2">
                @php
                    $suggestions = [
                        'Me surpreenda (baseado no meu gosto)',
                        'Na vibe da música...',
                        'Foco somente no artista...',
                        'Treino intenso',
                        'Focus mode'
                    ];
                @endphp
                @foreach($suggestions as $suggestion)
                    <button 
                        type="button" 
                        @disabled(!$hasApiKeys || $isGenerating) 
                        wire:click="applySuggestion('{{ $suggestion }}')" 
                        class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 {{ $hasApiKeys ? 'hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:text-purple-600 cursor-pointer active:scale-95' : 'cursor-not-allowed' }} text-zinc-600 dark:text-zinc-400 rounded-full transition-all whitespace-nowrap text-xs font-medium"
                    >
                        {{ $suggestion }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    @if($isGenerating || $isSaving)
        <!-- Full Screen Premium Loading Overlay -->
        <div 
            x-data="{ 
                step: 0, 
                messages: [
                    'Vamos lá! O Gemini está preparando algo incrível...',
                    'Analisando o clima da sua solicitação...',
                    'Explorando bilhões de possibilidades musicais...',
                    'Quase pronto! Organizando as faixas perfeitas...',
                    'Aguarde mais um pouco, a mágica está acontecendo...'
                ],
                init() {
                    setInterval(() => {
                        this.step = (this.step + 1) % this.messages.length;
                    }, 3500);
                }
            }"
            class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-2xl bg-black/70 animate-fade-in"
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

                    <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest animate-pulse">Processando pelo Gemini 1.5 Flash</p>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($tracks) && !$isGenerating && !$isSaving)
        <!-- Results State: Track List -->
        <div class="w-full max-w-7xl space-y-8 animate-fade-in-up">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-zinc-200 dark:border-zinc-800">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-widest text-purple-600">Resultado para</span>
                    <h2 class="text-3xl font-extrabold text-zinc-900 dark:text-white">"{{ $prompt }}"</h2>
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
