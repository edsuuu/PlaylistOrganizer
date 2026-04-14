<div class="rounded-2xl flex-1 flex flex-col min-w-0 bg-gradient-to-b from-gray-800 to-container-spotify p-6 border-b border-gray-800 relative">
    <div wire:loading wire:target="createNewPlaylist"
         class="fixed top-0 left-0 w-full h-full md:h-screen z-10">
        <div class="w-full h-full flex items-center justify-center bg-white/30 rounded-2xl">
            <x-heroicon-s-arrow-path class="text-green-spotify w-7 h-7 animate-spin"/>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div class="mt-6">
            <h1 class="text-3xl font-bold text-white mb-2">Organizador de Playlists</h1>
            <p class="text-gray-300">Organize e crie suas playlist muito mais rapido</p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6 pb-2">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-2">
            <button type="button" wire:click="createNewPlaylist" class="bg-gradient-to-br from-green-spotify to-green-600 p-2 px-4 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-bold text-lg whitespace-nowrap">Nova Playlist</h3>
                    </div>
                </div>
            </button>

            <a href="{{ route('ai-generator') }}" class="bg-gradient-to-br from-purple-600 to-pink-500 p-2 px-4 rounded-lg cursor-pointer hover:scale-105 transition-all shadow-lg shadow-purple-500/20 group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:icon.sparkles class="w-5 h-5 text-white/80 group-hover:text-white transition-colors" />
                        <h3 class="text-white font-bold text-lg whitespace-nowrap">Gerar com IA</h3>
                    </div>
                </div>
            </a>
        </div>
        @error('erroCreatePlaylist')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>
</div>
