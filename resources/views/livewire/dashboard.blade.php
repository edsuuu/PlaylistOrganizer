<div class="bg-container-spotify h-screen flex overflow-hidden text-white">

    <!-- Sidebar Esquerda -->
    <div class="w-64 bg-sidebar-spotify flex flex-col border-r border-gray-800">
        <!-- Logo/Header da Sidebar -->
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-spotify rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-sm">🎵</span>
                </div>
                <span class="font-bold text-lg">PlaylistOrganizer</span>
            </div>
        </div>

        <div class="flex-1 p-4 overflow-y-auto">
            <div class="space-y-2">
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    <span class="text-gray-300 group-hover:text-white font-medium">Home</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-gray-300 group-hover:text-white font-medium">Buscar</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="text-gray-300 group-hover:text-white font-medium">Biblioteca</span>
                </a>
            </div>

            <!-- Seção Playlists -->
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-400 font-semibold text-sm uppercase tracking-wider">Minhas Playlists</h3>
                    <button class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center gap-3 px-3 py-2 rounded-md bg-gray-800 cursor-pointer">
                        <div class="w-10 h-10 bg-green-spotify rounded flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 11-1.414-1.414A7.971 7.971 0 0017 12c0-1.594-.471-3.076-1.283-4.243a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 12a5.983 5.983 0 01-.757 2.829 1 1 0 11-1.415-1.414A3.987 3.987 0 0013 12a3.987 3.987 0 00-.172-1.415 1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">Favoritas</p>
                            <p class="text-gray-400 text-sm">125 músicas</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 cursor-pointer transition-colors">
                        <div class="w-10 h-10 bg-gray-600 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-sm">🎸</span>
                        </div>
                        <div>
                            <p class="text-gray-300 font-medium">Rock Clássico</p>
                            <p class="text-gray-400 text-sm">87 músicas</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 cursor-pointer transition-colors">
                        <div class="w-10 h-10 bg-purple-600 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-sm">🎹</span>
                        </div>
                        <div>
                            <p class="text-gray-300 font-medium">Chill Vibes</p>
                            <p class="text-gray-400 text-sm">63 músicas</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 cursor-pointer transition-colors">
                        <div class="w-10 h-10 bg-red-600 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-sm">🔥</span>
                        </div>
                        <div>
                            <p class="text-gray-300 font-medium">Workout</p>
                            <p class="text-gray-400 text-sm">45 músicas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex flex-col min-w-0">
        <div class="bg-gradient-to-b from-gray-800 to-container-spotify p-6 border-b border-gray-800">
            <div class="flex items-center justify-between">
                <div class="mt-6">
                    <h1 class="text-3xl font-bold text-white mb-2">Organizador de Playlists</h1>
                    <p class="text-gray-300">Organize e crie suas playlist muito mais rapido</p>
                </div>
            </div>
        </div>

        <!-- Área de Conteúdo Principal -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Cards de Ação Rápida -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-gradient-to-br from-green-spotify to-green-600 p-6 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg">Nova Playlist</h3>
                            <p class="text-green-100 text-sm">Criar coleção</p>
                        </div>
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-600 to-purple-700 p-6 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg">Importar</h3>
                            <p class="text-purple-100 text-sm">Do Spotify</p>
                        </div>
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-6 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg">Descobrir</h3>
                            <p class="text-blue-100 text-sm">Novas músicas</p>
                        </div>
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-600 to-orange-700 p-6 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg">Analytics</h3>
                            <p class="text-orange-100 text-sm">Estatísticas</p>
                        </div>
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-xl font-bold text-white mb-4">Playlists Recentes</h2>

                <div class="bg-sidebar-spotify rounded-lg p-4 hover:bg-gray-800 transition-colors cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-spotify to-green-600 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-white font-semibold text-lg">Minhas Favoritas</h3>
                            <p class="text-gray-400">125 músicas • Atualizada há 2 dias</p>
                            <div class="flex gap-2 mt-2">
                                <span class="bg-green-spotify bg-opacity-20 text-green-spotify px-2 py-1 rounded-full text-xs">Favoritas</span>
                                <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-full text-xs">Mix</span>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="bg-sidebar-spotify rounded-lg p-4 hover:bg-gray-800 transition-colors cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-700 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">🎹</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-white font-semibold text-lg">Chill Vibes</h3>
                            <p class="text-gray-400">63 músicas • Atualizada há 1 semana</p>
                            <div class="flex gap-2 mt-2">
                                <span class="bg-purple-600 bg-opacity-20 text-purple-400 px-2 py-1 rounded-full text-xs">Relax</span>
                                <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-full text-xs">Instrumental</span>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
