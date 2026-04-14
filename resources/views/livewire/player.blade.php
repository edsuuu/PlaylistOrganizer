<div
    wire:poll.5s="loadPlayback"
    wire:key="player-{{ $playback['item']['id'] ?? 'none' }}"
    x-data="{
        progress: {{ $playback['progress_ms'] ?? 0 }},
        duration: {{ $playback['item']['duration_ms'] ?? 1 }},
        isPlaying: {{ ($playback['is_playing'] ?? false) ? 'true' : 'false' }},
        volume: {{ $playback['device']['volume_percent'] ?? 50 }},
        
        init() {
            setInterval(() => {
                if (this.isPlaying && this.progress < this.duration) {
                    this.progress += 100;
                }
            }, 100);
        },
        
        formatMs(ms) {
            if (!ms) return '0:00';
            const minutes = Math.floor(ms / 60000);
            const seconds = Math.floor((ms % 60000) / 1000);
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    }"
    x-effect="
        progress = {{ $playback['progress_ms'] ?? 0 }};
        duration = {{ $playback['item']['duration_ms'] ?? 1 }};
        isPlaying = {{ ($playback['is_playing'] ?? false) ? 'true' : 'false' }};
        volume = {{ $playback['device']['volume_percent'] ?? 50 }};
    "
    class="fixed bottom-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-xl border-t border-zinc-800 text-white px-4 py-3 shadow-2xl"
>
    <div class="max-w-[1600px] mx-auto flex items-center justify-between gap-4">
        
        <!-- Track Info (Left) -->
        <div class="flex items-center gap-4 w-[30%] min-w-0">
            @if($playback)
                <img
                    src="{{ $playback['item']['album']['images'][0]['url'] ?? '/img/placeholder.png' }}"
                    class="w-14 h-14 rounded-lg shadow-lg"
                    alt="album"/>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm truncate text-white uppercase tracking-tight">{{ $playback['item']['name'] }}</p>
                    <p class="text-xs text-zinc-400 truncate">{{ $playback['item']['artists'][0]['name'] ?? 'Artista' }} </p>
                </div>
                <button class="hidden sm:block text-zinc-400 hover:text-white transition-colors">
                    <flux:icon.heart class="w-5 h-5" />
                </button>
            @else
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-zinc-800 rounded-lg flex items-center justify-center">
                        <flux:icon.musical-note class="w-6 h-6 text-zinc-600" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-bold text-zinc-400">Nenhuma música</p>
                        <p class="text-[10px] text-zinc-600 uppercase tracking-tighter">Inicie o Spotify</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Controls & Progress (Center) -->
        <div class="flex-1 max-w-2xl flex flex-col items-center gap-2">
            <!-- Buttons -->
            <div class="flex items-center gap-6">
                <button wire:click="previous" class="text-zinc-400 hover:text-white transition-colors">
                    <flux:icon.backward class="w-5 h-5 fill-current" />
                </button>
                
                <button 
                    @click="isPlaying ? $wire.pause() : $wire.resume()"
                    class="w-10 h-10 bg-white text-black rounded-full flex items-center justify-center hover:scale-105 transition-transform shadow-lg"
                >
                    <div x-show="!isPlaying">
                        <flux:icon.play class="w-6 h-6 fill-current ml-1" />
                    </div>
                    <div x-show="isPlaying" x-cloak>
                        <flux:icon.pause class="w-6 h-6 fill-current" />
                    </div>
                </button>

                <button wire:click="next" class="text-zinc-400 hover:text-white transition-colors">
                    <flux:icon.forward class="w-5 h-5 fill-current" />
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="w-full flex items-center gap-3">
                <span class="text-[10px] font-bold text-zinc-500 font-mono w-10 text-right" x-text="formatMs(progress)"></span>
                <div class="flex-1 h-1 bg-zinc-800 rounded-full overflow-hidden group relative">
                    <div
                        class="h-full bg-green-500 transition-all duration-100 ease-linear shadow-[0_0_8px_rgba(34,197,94,0.3)]"
                        :style="`width: ${Math.min((progress / duration) * 100, 100)}%`"
                    ></div>
                </div>
                <span class="text-[10px] font-bold text-zinc-500 font-mono w-10" x-text="formatMs(duration)"></span>
            </div>
        </div>

        <!-- Volume & Device (Right) -->
        <div class="w-[30%] flex items-center justify-end gap-6">
             <!-- Volume Slider -->
             <div class="hidden md:flex items-center gap-2 w-32 group">
                 <flux:icon.speaker-wave class="w-4 h-4 text-zinc-400 group-hover:text-white transition-colors" />
                 <input 
                    type="range" 
                    min="0" 
                    max="100" 
                    x-model="volume"
                    @change="$wire.setVolume(volume)"
                    class="w-full h-1 bg-zinc-800 rounded-full appearance-none cursor-pointer accent-green-500"
                 >
             </div>

             <!-- Device Info -->
             <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-zinc-800/50 rounded-full border border-zinc-700/50">
                 <flux:icon.computer-desktop class="w-3 h-3 text-green-500" />
                 <span class="text-[10px] font-bold text-zinc-300 uppercase tracking-tighter truncate max-w-[100px]">
                     {{ $playback['device']['name'] ?? 'Sem dispositivo' }}
                 </span>
             </div>
        </div>
    </div>
</div>
