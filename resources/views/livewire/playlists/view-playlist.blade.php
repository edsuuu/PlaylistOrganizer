<div>
    <div class="relative">
        <div class="relative flex items-center gap-6 p-6 pb-4">
            <div class="flex-shrink-0">
                @if(isset($this->playlistInfo['image']))
                    <img src="{{ $this->playlistInfo['image'] }}" alt="image-playlist" class="w-60 h-60 rounded-lg shadow-2xl flex items-center justify-center object-cover">
                @else
                    <div class="w-52 h-52 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg shadow-2xl flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <h1 class="text-5xl font-black text-white mb-4 truncate">{{ $this->playlistInfo['name'] }}</h1>
                <div class="flex items-center gap-2 text-sm text-gray-300">
                    <span> {{ $this->playlistInfo['tracks_total'] ?? 0 }} músicas</span>
                    <span> {{ $this->playlistInfo['owner_name'] ?? '' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
            <div class="add-music-card rounded-xl p-6 cursor-pointer group">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="pulse-ring absolute inset-0 rounded-full bg-purple-500 opacity-30"></div>
                        <div class="floating-icon w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-purple-300 transition-colors">
                            Adicionar Músicas
                        </h3>
                        <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">
                            Procure e adicione novas músicas à sua playlist
                        </p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="liked-music-card rounded-xl p-6 cursor-pointer group">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="pulse-ring absolute inset-0 rounded-full bg-green-500 opacity-30"></div>
                        <div class="floating-icon w-16 h-16 bg-gradient-to-br from-green-500 to-blue-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-green-300 transition-colors">
                            Músicas Curtidas
                        </h3>
                        <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">
                            Adicione músicas da sua biblioteca de curtidas
                        </p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            @if($canEditPlaylist)
                <div class="rounded-xl p-6 cursor-pointer group">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="pulse-ring absolute inset-0 rounded-full bg-orange-500 opacity-30"></div>
                        <div class="floating-icon w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-orange-300 transition-colors">
                            Editar Playlist
                        </h3>
                        <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">
                            Reordene e remova músicas da playlist
                        </p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="px-8">
        <div class="grid grid-cols-12 gap-4 px-4 py-2 text-sm text-gray-400 border-b border-gray-700 mb-2">
            <div class="col-span-1 text-center text-sm">#</div>
            <div class="col-span-5 text-sm">TÍTULO</div>
            <div class="col-span-3 text-sm">ÁLBUM</div>
            <div class="col-span-2 text-sm">ADICIONADA EM</div>
            <div class="col-span-1 text-center text-sm">
                TEMPO
            </div>
        </div>

        <div class="overflow-y-auto cst-scrollbar max-h-[450px]">
            <div class="space-y-1">
                @foreach($this->playlistTracks['tracks'] as $key => $track)
                    <div
                        class="grid grid-cols-12 gap-4 px-2 py-2 rounded-md hover:bg-white/30 hover:bg-opacity-10 transition-colors cursor-pointer" wire:key="{{ $track['id'] }}">
                        <div class="col-span-1 flex items-center justify-center">
                            <span class="text-gray-400">{{ $key + 1 }}</span>
                        </div>
                        <div class="col-span-5 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-600 rounded flex-shrink-0">
                                <img src="{{ $track['album_image'] }}" class="w-full h-full object-cover rounded" alt="album_image">
                            </div>
                            <div class="min-w-0">
                                <p class="text-white font-medium truncate">{{  $track['name'] ?? 'Sem nome' }}</p>
                                <p class="text-gray-400 text-sm truncate">{{  $track['artists_name'] ?? 'Sem nome' }}</p>
                            </div>
                        </div>
                        <div class="col-span-3 flex items-center">
                            <p class="text-gray-400 text-sm truncate">{{ $track['album_name'] ?? 'Album' }}</p>
                        </div>
                        <div class="col-span-2 flex items-center">
                            <p class="text-gray-400 text-sm">{{ $track['added_at_formated'] }}</p>
                        </div>
                        <div class="col-span-1 flex items-center justify-center">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400 text-sm">{{ $track['duration_ms'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
