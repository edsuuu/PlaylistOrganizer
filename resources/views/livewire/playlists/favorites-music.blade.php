<div>
    <div>
        <div wire:loading wire:target="addSingleMusicToPlaylist,addMultiplesMusicToPlaylist" class="absolute top-0 left-0 w-full h-full z-10">
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

            <div class="flex justify-between items-center gap-2 mx-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-green-spotify rounded-full flex items-center justify-center">
                        @if (Auth::user()->spotify->avatar)
                            <img src="{{ Auth::user()->spotify->avatar }}" class="rounded-full truncate" alt="avatar">
                        @else
                            <span class="text-white font-bold text-sm">🎵</span>
                        @endif
                    </div>
                    <span
                        class="font-bold text-l text-green-spotify">{{ Auth::user()->name ?? 'PlaylistOrganizer' }}</span>
                </div>
                <button wire:click="activeMultipleMusics"
                        class="border border-green-spotify rounded-lg text-green-spotify hover:bg-green-spotify hover:text-white transition-all duration-300 px-4 py-1 text-sm cursor-pointer">
                    {{ $activeMultipleMusicsToAddPlaylist ? 'Desativar seleção múltipla' : 'Ativar seleção múltipla' }}
                </button>
            </div>

            <small class="my-1 mx-4 text-white text-center">Músicas já adicionadas à playlist não serão incluídas
                novamente.</small>

            <div>
                <div class="grid grid-cols-12 gap-4 mx-4 py-1 text-sm text-gray-400 border-b border-gray-700 my-2">
                    <div class="col-span-1 text-center text-sm">#</div>
                    <div class="col-span-5 text-sm">TÍTULO</div>
                    <div class="col-span-3 text-sm">ÁLBUM</div>
                    <div class="col-span-1 text-center text-sm">TEMPO</div>
                    <div class="col-span-2 text-center text-sm">ADD</div>

                </div>

                <div class="overflow-y-auto cst-scrollbar max-h-[750px]"
                     x-data="{ isLoading: false }"
                     x-on:scroll="if (!isLoading && $el.scrollTop + $el.clientHeight >= $el.scrollHeight - 10 && {{ $musics['tracks'] ? count($musics['tracks']) : 0 }} < {{ $musics['total'] ?? 0 }}) {
                       isLoading = true;
                       $wire.loadMore().then(() => isLoading = false);
                   }">
                    <div class="space-y-1">
                        @foreach ($musics['tracks'] as $key => $music)
                            <label for="music-{{ $music['uri'] }}"
                                @class([
                                'grid grid-cols-12 gap-4 px-2 py-2 rounded-md hover:bg-white/30 hover:bg-opacity-10 transition-colors cursor-pointer',
                                'bg-white/30' => false
                        ]) wire:key="{{ $music['uri'] }}">
                                <div class="col-span-1 flex items-center justify-center">
                                    <span class="text-gray-400">{{ $key + 1 }}</span>
                                </div>
                                <div class="col-span-5 flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-600 rounded flex-shrink-0">
                                        <img src="{{ $music['album_image'] ?? 1 }}"
                                             class="w-full h-full object-cover rounded"
                                             alt="album_image">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-white font-medium truncate">{{  $music['name'] ?? 'Sem nome' }}</p>
                                        <p class="text-gray-400 text-sm truncate">{{  $music['artists_name'] ?? 'Sem nome' }}</p>
                                    </div>
                                </div>
                                <div class="col-span-3 flex items-center">
                                    <p class="text-gray-400 text-sm truncate">{{ $music['album_name'] ?? 'Album' }}</p>
                                </div>
                                <div class="col-span-1 flex items-center justify-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 text-sm">{{ $music['duration_ms'] ?? 1 }}</span>
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-center justify-center">
                                    @if ($activeMultipleMusicsToAddPlaylist)
                                        <input type="checkbox" id="music-{{ $music['uri'] }}"
                                               wire:click="toggleTrack('{{ $music['uri'] }}')"
                                    @else
                                        <span wire:click="addSingleMusicToPlaylist('{{ $music['uri'] }}')"
                                              class="border border-green-spotify rounded-lg p-2 text-green-spotify text-[12px] hover:bg-green-spotify hover:text-white transition-all duration-300 cursor-pointer">+ Playlist</span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @if($activeMultipleMusicsToAddPlaylist)
                        <div class="fixed bottom-8 right-8">
                            <button type="button" wire:click="addMultiplesMusicToPlaylist"
                                    class="px-6 py-3 bg-green-spotify text-white rounded-full hover:bg-green-600 transition-colors cursor-pointer shadow-lg">
                                Adicionar
                            </button>
                        </div>
                    @endif
                    <div wire:loading wire:target="loadMore" class="relative w-full h-1 bg-transparent overflow-hidden">
                        <div class="absolute top-0 right-0 h-full w-full bg-green-spotify"
                             style="animation: loadingLine 1.2s linear infinite;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('OpenModalCenter', () => {
        window.dispatchEvent(new CustomEvent('openmodal'));
    });
</script>
