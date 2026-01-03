<div
    wire:poll.1="loadPlayback"
    class="w-full mx-auto bg-neutral-900 text-white rounded-xl p-4 shadow-lg"
>
    <div>
        <div class="flex gap-4 items-center">
            <img
                src="{{ $playback['item']['album']['images'][0]['url'] }}"
                class="w-20 h-20 rounded-md"
                alt="album"/>

            <div class="flex-1">
                <p class="font-semibold text-lg truncate">{{ $playback['item']['name'] }}</p>
                <p class="text-sm text-neutral-400 truncate">{{ $playback['item']['artists'][0]['name'] }} </p>
                <p class="text-xs text-neutral-500 truncate">{{  $playback['item']['album']['name']  }}</p>
            </div>
        </div>

        <div class="mt-4">
            <div class="w-full h-1 bg-neutral-700 rounded-full overflow-hidden">
                <div
                    class="h-1 bg-green-spotify transition-all duration-100 ease-linear"
                    :style="`width: {{ floor(($playback['progress_ms'] / $playback['item']['duration_ms']) * 100) ?? 0 }}%`"
                ></div>
                </div>

                <div class="flex justify-between text-xs text-neutral-400 mt-1">
                    <span >{{ $this->formatDuration($playback['progress_ms']) }}</span>
                    <span >{{ $this->formatDuration($playback['item']['duration_ms']) }}</span>
                </div>
        </div>

        <div class="mt-3 text-xs text-neutral-400 flex justify-between">
            <span>{{ $playback['is_playing'] ? '▶ Tocando' : '⏸ Pausado' }}</span>
            <span class="text-bg-green-spotify">💻 {{ $playback['device']['type']  }} - {{ $playback['device']['name'] }}</span>
            <span>🔊 {{ $playback['device']['volume_percent'] }}%</span>
        </div>
    </div>
</div>
