<div class="flex-1 flex flex-col min-w-0">
    <div class="bg-gradient-to-b from-gray-800 to-container-spotify p-6 border-b border-gray-800">
        <div class="flex items-center justify-between">
            <div class="mt-6">
                <h1 class="text-3xl font-bold text-white mb-2">Organizador de Playlists</h1>
                <p class="text-gray-300">Organize e crie suas playlist muito mais rapido</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6 pb-2">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-2">
            <button type="button" wire:click="createNewPlaylist" class="bg-gradient-to-br from-green-spotify to-green-600 p-2 px-4 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-bold text-lg">Nova Playlist</h3>
                    </div>
                </div>
            </button>
        </div>
        @error('erroCreatePlaylist')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>
</div>
