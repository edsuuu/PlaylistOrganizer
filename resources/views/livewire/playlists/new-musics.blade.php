<div class="relative">
    <div wire:loading wire:target="searchMusics,addMultiplesMusicToPlaylist,addSingleMusicToPlaylist"
         class="fixed top-0 left-0 w-full h-full md:h-screen z-10">
        <div class="w-full h-full flex items-center justify-center bg-white/30 rounded-2xl">
            <x-heroicon-s-arrow-path class="text-green-spotify w-7 h-7 animate-spin"/>
        </div>
    </div>

    <div x-data="{ showModal: false }" @openmodal.window="showModal = true">
        <div x-show="showModal"
             x-cloak
             class="absolute inset-0 bg-white/20 z-40"
             @click="showModal = false">
        </div>

        <div x-show="showModal"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50"
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

    <div class="bg-gray-800 rounded-2xl p-6">

        <div class="flex items-center gap-10 ">
            <div class="flex gap-4 items-center">
                @isset($this->playlistInfo['image'])
                    <div class="flex-shrink-0">
                            <img src="{{ $this->playlistInfo['image'] }}" alt="image-playlist"
                                 class="w-12 h-12 rounded-lg shadow-2xl flex items-center justify-center object-cover">
                    </div>
                @endisset

                <div class="flex-1 min-w-0">
                    <h1 class="text-xl font-black text-white">{{ $this->playlistInfo['name'] }}</h1>
                </div>
            </div>
        </div>

            <div class="w-full flex flex-col md:flex-row gap-4 mt-4">
                @if(!$favoriteMusic)
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           class="w-full py-2 pl-4 border border-green-spotify rounded-lg text-white placeholder-gray-400 outline-none focus:border-green-spotify"
                           placeholder="Pesquisar música, artista ou álbum...">
                @endif

                <div class="flex flex-col md:flex-row gap-4 flex-shrink-0">
                    @if(!$favoriteMusic)
                        {{-- todo: lembrar de incluir o input de pesquisa para musicas favoritas                        --}}

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
                    @endif
                    <button wire:click="activeMultipleMusics"
                            class="flex items-center p-1 gap-3 cursor-pointer rounded-lg transition-all duration-300 whitespace-nowrap hover:bg-green-spotify/30">
                        <div
                            class="flex-shrink-0 w-7 h-7 bg-green-spotify/20 rounded-full flex items-center justify-center">
                            <x-heroicon-s-plus class="w-5 h-5 text-green-spotify"/>
                        </div>
                        <div class="text-left">
                            <h3 class="text-white font-medium">{{ $activeMultipleMusicsToAddPlaylist ? 'Desativar' : 'Ativar' }}
                                Múltiplas escolha de músicas</h3>
                            <p class="text-gray-400 text-sm max-md:hidden">Adicionar várias músicas de uma vez</p>
                        </div>
                    </button>
                </div>
            </div>
    </div>

    @isset($this->playlistTracks['tracks'])
        <div class="rounded-2xl bg-gradient-to-b from-gray-800 to-container-spotify relative p-4 mt-6 ">
            @if($favoriteMusic)
                <h1 class="ml-2 my-2 text-gray-400">Músicas curtidas</h1>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->playlistTracks['tracks'] as $key => $track)
                    <div wire:key="{{ $track['uri'] }}"
                         @class(['rounded-xl p-4 flex flex-col gap-3 shadow-md hover:shadow-xl transition transform hover:-translate-y-0.5 cursor-pointer',
                                'bg-white/30' => in_array($track['uri'], array_column($selectedTracks, 'uri'))])
                         @if($activeMultipleMusicsToAddPlaylist)
                             wire:click="toggleTrack('{{ $track['uri'] }}')"
                        @endif
                    >

                        <div class="flex gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-700 flex-shrink-0">
                                    <img src="{{ $track['album_image'] }}" alt="album_image"
                                         class="object-cover w-full h-full"/>
                                </div>
                                <div>
                                    <p class="text-white text-sm font-semibold">{{  $track['name'] ?? 'Sem nome' }}</p>
                                    <p class="text-gray-400 text-xs">{{  $track['artists_name'] ?? 'Sem nome' }}</p>
                                </div>
                            </div>
                        </div>


                        <div class="flex items-center justify-between text-xs text-gray-400 mt-1">
                            <span class="truncate">{{ $track['album_name'] ?? 'Album' }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-gray-400 text-xs">{{ $track['duration_ms'] }}</span>

                                @if(!$activeMultipleMusicsToAddPlaylist)
                                    <button type="button" wire:click="addSingleMusicToPlaylist('{{$track['uri']}}')"
                                            class="p-1 rounded-md hover:bg-white/5 transition cursor-pointer">

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>

                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <div x-intersect="$wire.loadMore()" class="h-4 w-full"></div>
            </div>

            <div wire:loading wire:target="loadMore" class="relative w-full h-1 bg-transparent overflow-hidden">
                <div class="absolute top-0 right-0 h-full w-full bg-green-spotify"
                     style="animation: loadingLine 1.2s linear infinite;"></div>
            </div>
        </div>
    @endisset

    <div class="fixed bottom-4 right-10 flex items-center justify-center mt-5">
        <button wire:click="addMultiplesMusicToPlaylist" @class([
            'bg-green-spotify text-black py-2 px-4 rounded-full hover:scale-105 transition-all duration-300 cursor-pointer',
            'hidden' => !$activeMultipleMusicsToAddPlaylist,
            'opacity-50 cursor-not-allowed' => empty($selectedTracks)
        ])>
            Adicionar {{ count($selectedTracks) }} músicas
        </button>
    </div>
</div>
