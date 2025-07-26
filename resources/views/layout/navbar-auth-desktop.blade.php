<div class="flex flex-row justify-between lg:hidden bg-blue-black">
    <button
        x-data
        x-on:click="$dispatch('toggle-sidebar')"
        class="p-2 pl-4"
    >
        <x-heroicon-s-bars-3-bottom-left class="w-6 h-6 text-white"/>
    </button>

    <header class="pl-3 md:px-4 py-3 flex justify-between items-center">
        <div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
            <button @click="open = !open" class="flex gap-2 items-center cursor-pointer">
                    <div class="shrink-0 w-6 h-6 bg-gray-300 rounded-md flex items-center justify-center">
                        <span class="font-medium text-sm">{{ strtoupper(Str::substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="shrink-0 flex">
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-white" />
                    </div>
            </button>

            <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-[90vw] max-w-[300px] bg-white rounded-md shadow-2xl z-10" style="display: none;">
                <div class="border-b border-gray-200 p-4">
                    <div class="flex flex-row items-center gap-2 ">
                        <div class="shrink-0 w-7 h-7 p-2 bg-gray-300 rounded-md flex items-center justify-center">
                            <span class="font-medium text-sm">{{ strtoupper(Str::substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="flex flex-col overflow-hidden">
                            <span class="font-semibold text-sm truncate">{{ auth()->user()->name  }}</span>
                            <span class="text-sm text-zinc-500 truncate">{{ auth()->user()->email  }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-2">
                    <a href="{{ route('configuration') }}" wire:navigate class="flex items-center gap-3 p-2 text-zinc-700 hover:bg-zinc-100 transition-colors">
                        <x-heroicon-o-cog class="w-5 h-5 text-gray-400"/>
                        <span class="text-sm">Configurações</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}"  class="flex items-center gap-3 p-2 text-zinc-700 hover:bg-zinc-100 transition-colors cursor-pointer">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 cursor-pointer">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 text-gray-400"/>
                            <span class="text-sm">Sair</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
</div>


<div class="hidden lg:block fixed inset-y-0 left-0 w-64 bg-blue-black transform transition-transform duration-200 ease-in-out translate-x-0">
    @include('layout.navbar-links')
</div>
