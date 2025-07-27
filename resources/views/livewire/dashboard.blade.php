<div class="flex-1 flex flex-col min-w-0">
    <div class="bg-gradient-to-b from-gray-800 to-container-spotify p-6 border-b border-gray-800">
        <div class="flex items-center justify-between">
            <div class="mt-6">
                <h1 class="text-3xl font-bold text-white mb-2">Organizador de Playlists</h1>
                <p class="text-gray-300">Organize e crie suas playlist muito mais rapido</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div
                class="bg-gradient-to-br from-green-spotify to-green-600 p-6 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-bold text-lg">Nova Playlist</h3>
                        <p class="text-green-100 text-sm">Criar coleção</p>
                    </div>
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h2 class="text-xl font-bold text-white mb-4">Playlists Recentes</h2>

            {{--                <div class="bg-sidebar-spotify rounded-lg p-4 hover:bg-gray-800 transition-colors cursor-pointer">--}}
            {{--                    <div class="flex items-center gap-4">--}}
            {{--                        <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-700 rounded-lg flex items-center justify-center">--}}
            {{--                            <span class="text-white font-bold text-xl">🎹</span>--}}
            {{--                        </div>--}}
            {{--                        <div class="flex-1">--}}
            {{--                            <h3 class="text-white font-semibold text-lg">Chill Vibes</h3>--}}
            {{--                            <p class="text-gray-400">63 músicas • Atualizada há 1 semana</p>--}}
            {{--                            <div class="flex gap-2 mt-2">--}}
            {{--                                <span class="bg-purple-600 bg-opacity-20 text-purple-400 px-2 py-1 rounded-full text-xs">Relax</span>--}}
            {{--                                <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-full text-xs">Instrumental</span>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                        <button class="text-gray-400 hover:text-white transition-colors">--}}
            {{--                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
            {{--                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>--}}
            {{--                            </svg>--}}
            {{--                        </button>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
        </div>
    </div>
</div>
