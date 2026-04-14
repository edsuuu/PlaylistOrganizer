<div wire:init="syncLikedSongs" class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-3">
                Músicas Curtidas
                @if($isSyncing)
                    <flux:icon.arrow-path class="animate-spin text-purple-500 w-6 h-6" />
                @endif
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400">Suas faixas favoritas sincronizadas do Spotify</p>
        </div>

        <div class="flex items-center gap-3">
            <flux:button 
                wire:click="checkDuplicates" 
                variant="outline" 
                icon="square-2-stack" 
                class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800"
                wire:loading.attr="disabled"
            >
                @if($isChecking)
                    <flux:icon.arrow-path class="animate-spin w-4 h-4 mr-2" />
                @endif
                Validar Duplicadas
            </flux:button>

            <div class="w-full md:w-64">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Pesquisar músicas..." 
                    icon="magnifying-glass"
                    class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800"
                />
            </div>
            <div class="text-right flex-shrink-0 min-w-[80px]">
                <span class="text-xs font-semibold uppercase tracking-wider text-purple-500 dark:text-purple-400">Total</span>
                <div class="text-2xl font-bold dark:text-white">
                    {{ $totalDb }} <span class="text-zinc-400 text-sm">/ {{ $totalSpotify }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Duplicadas --}}
    <flux:modal name="duplicates-modal" x-on:show-duplicates.window="$flux.modal('duplicates-modal').show()" class="md:w-[600px] bg-zinc-900 border-zinc-800" variant="flyout">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl" class="text-white">Músicas Duplicadas</flux:heading>
                <flux:subheading>Encontramos {{ count($duplicates) }} possíveis duplicadas nas suas curtidas.</flux:subheading>
            </div>

            @if(count($duplicates) > 0)
                <div class="flex justify-between items-center bg-zinc-800/30 p-2 rounded-lg border border-zinc-700/50">
                    <span class="text-xs text-zinc-400">{{ count($duplicatePositions) }} selecionadas</span>
                    <button 
                        @click="$wire.duplicatePositions = $wire.duplicates.map(d => d.position)" 
                        type="button"
                        class="text-xs text-purple-400 hover:text-purple-300 font-medium"
                    >
                        Selecionar Tudo
                    </button>
                </div>

                <div class="max-h-[60vh] overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                    @foreach($duplicates as $dupe)
                        <label class="flex items-center gap-4 p-3 bg-zinc-800/10 hover:bg-zinc-800/30 rounded-lg border border-zinc-800 hover:border-zinc-700 transition cursor-pointer group">
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="duplicatePositions" 
                                    value="{{ $dupe['position'] }}" 
                                    class="rounded border-zinc-700 bg-zinc-900 text-purple-600 focus:ring-purple-500"
                                >
                            </div>
                            <img src="{{ $dupe['image'] }}" class="w-12 h-12 rounded object-cover shadow-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-white truncate">{{ $dupe['name'] }}</p>
                                <p class="text-xs text-zinc-400 truncate">{{ $dupe['artist'] }}</p>
                                <div class="mt-1">
                                    <span class="text-[9px] uppercase tracking-tighter text-orange-400 font-black px-1.5 py-0.5 bg-orange-400/10 rounded ring-1 ring-orange-400/20">{{ $dupe['reason'] }}</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex gap-3 justify-end pt-4 border-t border-zinc-800">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button 
                        wire:click="removeSelectedDuplicates" 
                        variant="primary" 
                        class="bg-red-500 hover:bg-red-600 border-none px-6"
                        wire:loading.attr="disabled"
                    >
                        Remover da Biblioteca
                    </flux:button>
                </div>
            @else
                <div class="flex flex-col items-center gap-4 py-12">
                    <flux:icon.check-circle class="w-16 h-16 text-green-500 opacity-50" />
                    <p class="text-zinc-400">Nenhuma duplicada encontrada!</p>
                </div>
            @endif
        </div>
    </flux:modal>

    @if($isSyncing)
        <div class="bg-purple-100 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 p-4 rounded-xl flex items-center gap-4 transition-all animate-pulse">
            <flux:icon.sparkles class="text-purple-600 dark:text-purple-400 w-6 h-6" />
            <div class="flex-1">
                <span class="text-purple-800 dark:text-purple-300 font-medium text-sm">Sincronizando novas músicas...</span>
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-purple-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $totalSpotify > 0 ? ($totalDb / $totalSpotify) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    @endif

    <div class="overflow-hidden bg-white dark:bg-zinc-900/50 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 font-semibold">#</th>
                    <th class="px-6 py-4 font-semibold">Título</th>
                    <th class="px-6 py-4 font-semibold hidden md:table-cell">Álbum</th>
                    <th class="px-6 py-4 font-semibold text-right">
                        <flux:icon.clock class="w-4 h-4 ml-auto" />
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($tracks as $index => $track)
                    <tr 
                        wire:click="play('{{ data_get($track, 'uri') }}')"
                        class="group hover:bg-zinc-100/50 dark:hover:bg-zinc-800/30 transition-colors cursor-pointer"
                    >
                        <td class="px-6 py-4 text-zinc-400 text-sm">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="relative w-12 h-12 flex-shrink-0 group/img cursor-pointer">
                                    <img src="{{ data_get($track, 'image') ?? '/img/placeholder.png' }}" alt="{{ data_get($track, 'name') }}" class="w-full h-full rounded-lg shadow-md group-hover/img:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center rounded-lg opacity-0 group-hover/img:opacity-100 transition-all duration-300">
                                        <flux:icon.play class="w-6 h-6 text-white fill-current shadow-xl" />
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-purple-600 transition-colors line-clamp-1">
                                        {{ data_get($track, 'name') }}
                                    </h4>
                                    <p class="text-xs text-zinc-500 line-clamp-1">{{ data_get($track, 'artist') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500 hidden md:table-cell">
                            {{ data_get($track, 'album') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-400 text-right font-mono">
                            @php
                                $ms = data_get($track, 'duration_ms') ?? 0;
                            @endphp
                            {{ floor(($ms / 1000) / 60) }}:{{ str_pad(round(($ms / 1000) % 60), 2, '0', STR_PAD_LEFT) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <flux:icon.musical-note class="w-12 h-12 text-zinc-300 dark:text-zinc-700" />
                                <p class="text-zinc-500">Nenhuma música encontrada.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(!$loadFromApi && count($tracks) >= $perPage)
        <div x-intersect="$wire.loadMore()" class="h-10 w-full flex items-center justify-center">
            <flux:icon.arrow-path class="animate-spin text-purple-500 w-6 h-6" />
        </div>
    @endif
</div>
