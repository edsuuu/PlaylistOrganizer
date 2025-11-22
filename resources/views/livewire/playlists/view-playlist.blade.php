<div class="relative">
    <div wire:loading wire:target="deleteSingleTrack,deleteSelectedTracks" class="absolute top-0 left-0 w-full h-full md:h-screen z-10">
        <div class="w-full h-full rounded-md flex items-center justify-center bg-white/30">
            <x-heroicon-s-arrow-path class="text-green-spotify w-10 h-10 animate-spin"/>
        </div>
    </div>

    <div class="rounded-2xl bg-gray-800 pb-2">
        <div class="flex flex-col items-center sm:flex-row gap-6 p-6">
            <div class="w-max-[300px] h-max-[300px] md:32 md:h-32 rounded-lg shadow-2xl">
                @isset($this->playlistInfo['image'])
                    <img src="{{ $this->playlistInfo['image'] }}" alt="image-playlist"
                         class="w-full h-full object-cover rounded-lg">
                @else
                    <div
                        class="w-full h-full bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg shadow-2xl flex items-center justify-center">
                        <svg class="w-32 h-32 text-white opacity-80" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                        </svg>
                    </div>
                @endisset
            </div>

            <div class="flex-1">
                <h1 class="text-xl md:text-3xl font-black text-white mb-4">{{ $this->playlistInfo['name'] ?? 'Músicas curtidas' }}</h1>
                <div class="flex items-center gap-2 text-sm text-gray-300">
                    @if($favoritePlaylist)
                        <span class="break-words"> {{ $this->playlistTracks['total'] ?? 0 }} músicas - {{ auth()->user()->name }}</span>
                    @else
                        <span class="break-words"> {{ $this->playlistInfo['tracks_total'] ?? 0 }} músicas - {{ $this->playlistInfo['owner_name'] ?? '' }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="px-4">
            @if($canEditPlaylist)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">

                    <div x-data="{ open: false }" class="add-music-card rounded-xl p-6 cursor-pointer group relative border border-gray-700/50">

                        <button @click="open = !open" class="flex items-center gap-4 w-full text-left cursor-pointer">
                            <div class="flex-1">
                                <h3 class="select-none text-xl font-bold text-white mb-2 group-hover:text-purple-300 transition-colors">
                                    Adicionar Músicas
                                </h3>
                                <p class="select-none text-gray-400 text-sm group-hover:text-gray-300 transition-colors">
                                    Procure e adicione novas músicas à sua playlist
                                </p>
                            </div>
                            <div class="transition-transform duration-300"
                                 :class="open ? 'rotate-180 opacity-100' : 'opacity-50 group-hover:opacity-100'">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        <div
                            x-show="open"
                            x-transition.origin.top.left
                            @click.away="open = false"

                            class="absolute left-0 mt-3 w-full md:w-64 bg-neutral-900 border border-neutral-700 rounded-xl shadow-2xl p-2 z-20 space-y-1"
                        >

                            <a href="{{ route('new-musics-playlist', ['id' => $playlistId]) }}" wire:navigate
                               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-neutral-800 transition cursor-pointer group">

                                <svg class="w-5 h-5 text-purple-300 opacity-80 group-hover:opacity-100"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 15l5 5m-5-5A6 6 0 1 0 3 9a6 6 0 0 0 12 0Z"/>
                                </svg>

                                <span class="text-sm text-gray-300 group-hover:text-white">Pesquisar músicas</span>
                            </a>


                            <a href="{{ route('liked-musics-playlist', ['id' => $playlistId]) }}" wire:navigate
                                class="flex items-center gap-3 px-4 py-3 rounded-lg w-full text-left hover:bg-neutral-800 transition cursor-pointer group">

                                <svg class="w-5 h-5 text-green-300 opacity-80 group-hover:opacity-100"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M20.8 6.6a4.5 4.5 0 0 0-6.36 0L12 9 9.56 6.59a4.5 4.5 0 1 0-6.36 6.36L12 21l8.8-8.04a4.5 4.5 0 0 0 0-6.36z"/>
                                </svg>

                                <span class="text-sm text-gray-300 group-hover:text-white">Músicas curtidas</span>
                            </a>

                        </div>
                    </div>

                    <div wire:click="toggleDelete"
                         class="rounded-xl p-6 cursor-pointer group border border-gray-700/50">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <h3 class="select-none text-xl font-bold text-white mb-2 group-hover:text-orange-300 transition-colors">
                                    Excluir músicas da playlist
                                </h3>
                                <p class="select-none text-gray-400 text-sm group-hover:text-gray-300 transition-colors">
                                    Remover varias músicas da playlist
                                </p>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>

    <div class="rounded-2xl bg-gradient-to-b from-gray-800 to-container-spotify relative p-4 mt-6 ">
        <h1 class="ml-2 my-2 text-gray-400">Músicas</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($this->playlistTracks['tracks'] as $key => $track)
                <div wire:key="{{ $track['uri'] }}"
                         @class(['rounded-xl p-4 flex flex-col gap-3 shadow-md hover:shadow-xl transition transform hover:-translate-y-0.5 cursor-pointer',
                                'bg-white/30' => in_array($track['uri'], array_column($selectedTracks, 'uri'))])
                     @if($editMusics)
                         wire:click="toggleTrack('{{ $track['uri'] }}')"
                     @endif
                >
                    <div class="flex justify-end text-xs text-gray-400 mt-1">
                        <span>Adicionada {{ $track['added_at_formated'] }}</span>
                    </div>

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

                            @if($canEditPlaylist && !$editMusics)
                                <button type="button" wire:click="deleteSingleTrack('{{$track['uri']}}')"
                                        class="p-1 rounded-md hover:bg-white/5 transition cursor-pointer">
                                    <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
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

        @if($editMusics)
            <div class="fixed bottom-4 right-8 flex gap-2">
                <button wire:click="deleteSelectedTracks"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md">
                    Apagar
                </button>
                <button wire:click="toggleDelete"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                    Cancelar
                </button>
            </div>
        @endif
    </div>
</div>
