<div class="bg-gradient-to-b from-gray-800 to-container-spotify relative px-4">
        <div wire:loading wire:target="searchMusics,addMultiplesMusicToPlaylist" class="absolute top-0 left-0 w-full h-full z-10">
            <div class="w-full h-full rounded-md flex items-center justify-center bg-white/30">
                <x-heroicon-s-arrow-path class="text-green-spotify w-7 h-7 animate-spin"/>
            </div>
        </div>

    <div x-data="{ showModal: false }" @openmodal.window="showModal = true">
        <div x-show="showModal"
             class="absolute inset-0 bg-white/20 z-40"
             @click="showModal = false">
        </div>

        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="relative">
                <div class="absolute inset-0 rounded-lg"></div>
                <div class="relative bg-[#282828] rounded-lg p-6 max-w-sm w-full">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-white mb-4 text-center">Essa música já está na
                            playlist @if ($musicRepetitive > 1)
                                , ela aparece {{ $musicRepetitive }} vezes
                            @endif</h3>
                        <button @click="showModal = false"
                                class="mt-4 px-4 py-2 bg-green-spotify text-white rounded-full hover:bg-green-600 transition-colors cursor-pointer">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>

    <div class="flex items-center gap-10 p-6">
        <div class="flex gap-4 items-center">
            <div class="flex-shrink-0">
                @if(isset($this->playlistInfo['image']))
                    <img src="{{ $this->playlistInfo['image'] }}" alt="image-playlist"
                         class="w-12 h-12 rounded-lg shadow-2xl flex items-center justify-center object-cover">
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-black text-white truncate">{{ $this->playlistInfo['name'] }}</h1>
            </div>
        </div>
    </div>


    <div class="flex flex-row gap-4 items-center px-4">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               class="flex-1 py-2 pl-4 border border-green-spotify rounded-lg text-white placeholder-gray-400 outline-none focus:border-green-spotify"
               placeholder="Pesquisar música, artista ou álbum...">
        <div class="flex flex-row gap-4 flex-shrink-0">
            <button wire:click="clearSearch"
                    class="flex items-center p-1 gap-3 cursor-pointer rounded-lg transition-all duration-300 whitespace-nowrap hover:bg-green-spotify/30">
                <div
                    class="flex-shrink-0 w-7 h-7 bg-green-spotify/20 rounded-full flex items-center justify-center">
                    <x-heroicon-s-x-mark class="w-5 h-5 text-green-spotify"/>
                </div>
                <div class="text-left">
                    <h3 class="text-white font-medium">Limpar pesquisa</h3>
                </div>
            </button>
            <button wire:click="activeMultipleMusics"
                    class="flex items-center p-1 gap-3 cursor-pointer rounded-lg transition-all duration-300 whitespace-nowrap hover:bg-green-spotify/30">
                <div
                    class="flex-shrink-0 w-7 h-7 bg-green-spotify/20 rounded-full flex items-center justify-center">
                    <x-heroicon-s-plus class="w-5 h-5 text-green-spotify"/>
                </div>
                <div class="text-left">
                    <h3 class="text-white font-medium">{{ $activeMultipleMusicsToAddPlaylist ? 'Desativar' : 'Ativar' }} Múltiplas Músicas</h3>
                    <p class="text-gray-400 text-sm">Adicionar várias músicas de uma vez</p>
                </div>
            </button>
        </div>
    </div>

    <div class="px-4">
        <div class="overflow-y-auto cst-scrollbar mt-4 max-h-[550px]" x-data="{ isLoading: false }"
             x-on:scroll="if (!isLoading && $el.scrollTop + $el.clientHeight >= $el.scrollHeight - 10 && {{ isset($this->playlistTracks['tracks']) ? count($this->playlistTracks['tracks']) : 0 }} < {{ isset($this->playlistTracks['tracks']) ? $this->playlistTracks['total'] : 0 }}) {
           isLoading = true;
           $wire.loadMore().then(() => isLoading = false);
       }">
            <div class="space-y-1">
                @if(isset($this->playlistTracks['tracks']))

                    @foreach($this->playlistTracks['tracks'] as $key => $track)
                        <label for="addmusic{{$key}}" @class([
                                'grid grid-cols-12 gap-4 px-2 py-2 rounded-md hover:bg-white/30 hover:bg-opacity-10 transition-colors cursor-pointer',
//                                'bg-white/30' => collect($selectedTracks)->contains('id', $track['id']) && collect($selectedTracks)->contains('index', $key)
                                'bg-white/30' => false
                            ])
                        wire:key="{{ $track['uri'] }}"
                        >
                            <div class="col-span-1 flex items-center justify-center">
                                <span class="text-gray-400">{{ $key + 1 }}</span>
                            </div>
                            <div class="col-span-5 flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-600 rounded flex-shrink-0">
                                    <img src="{{ $track['album_image'] }}" class="w-full h-full object-cover rounded"
                                         alt="album_image">
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white font-medium truncate">{{  $track['name'] ?? 'Sem nome' }}</p>
                                    <p class="text-gray-400 text-sm truncate">{{  $track['artists_name'] ?? 'Sem nome' }}</p>
                                </div>
                            </div>
                            <div class="col-span-3 flex items-center">
                                <p class="text-gray-400 text-sm truncate">{{ $track['album_name'] ?? 'Album' }}</p>
                            </div>
                            <div class="col-span-1 flex items-center justify-center">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400 text-sm">{{ $track['duration_ms'] }}</span>
                                </div>
                            </div>
                            <div class="col-span-2 flex items-center justify-center">
                                @if ($activeMultipleMusicsToAddPlaylist)
                                    <input type="checkbox" id="addmusic{{$key}}" wire:click="toggleTrack('{{ $track['uri'] }}')" wire:change="handleTrackSelection('{{ $track['uri'] }}', $event.target.checked)"
                                    />
                                @else
                                    <span wire:click="addSingleMusicToPlaylist('{{ $track['uri'] }}')"
                                          class="border border-green-spotify rounded-lg p-2 text-green-spotify text-[12px] hover:bg-green-spotify hover:text-white transition-all duration-300 cursor-pointer">+ Playlist</span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                @endif

            </div>
            <div wire:loading wire:target="loadMore" class="relative w-full h-1 bg-transparent overflow-hidden">
                <div class="absolute top-0 right-0 h-full w-full bg-green-spotify"
                     style="animation: loadingLine 1.2s linear infinite;"></div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center mt-5">
        <button wire:click="addMultiplesMusicToPlaylist" @class([
            'bg-green-spotify text-black py-2 px-4 rounded-full hover:scale-105 transition-all duration-300 cursor-pointer',
            'hidden' => !$activeMultipleMusicsToAddPlaylist,
            'opacity-50 cursor-not-allowed' => empty($selectedTracks)
        ])>
            Adicionar {{ count($selectedTracks) }} músicas
        </button>
    </div>
</div>
