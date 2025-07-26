<div
    x-data="{ open: @entangle($attributes->wire('model')) }"
    x-cloak
    x-show="open"
    class="fixed inset-0 bg-black/50 flex items-center justify-center"
    @keydown.escape.window="open = false"
>
    <div
        x-show="open"
        x-transition:enter="transition duration-300 transform"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition duration-200 transform"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative"
        @click.away="open = false"
    >
        <button class="absolute top-2 right-2 text-gray-600 text-xl" @click="open = false">X</button>

        @if($title)
            <h2 class="text-xl font-semibold mb-4">{{ $title }}</h2>
        @endif

        {{ $slot }}
    </div>
</div>
