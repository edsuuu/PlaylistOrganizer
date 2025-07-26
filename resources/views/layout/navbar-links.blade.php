<div class="flex flex-col gap-5 h-screen">
    <a href="{{ route('home-tenant') }}" class="flex flex-row items-center gap-4 p-4">
        <div class="bg-black p-2 rounded-md">
            <x-heroicon-m-computer-desktop class="w-5 h-5 text-white"/>
        </div>

        <span class="font-semibold text-white truncate">{{ auth()->user()->tenant?->name ?? 'Administrador' }}</span>
    </a>

    <div class="flex-1 overflow-y-auto pr-1 pb-5 cst-scrollbar">
        <div class="flex flex-col gap-2 px-2">
            <p class="p-2 text-white text-sm">Plataforma </p>

            <a href="{{ route('dashboard') }}" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-link' : '' }}">
                <div class="flex flex-row gap-2 items-center p-2">
                    <x-heroicon-o-home class="w-6 h-6"/>
                    <span class="">Dashboard </span>
                </div>
            </a>

                <a href="#" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('home') && !request('status') ? 'bg-blue-link' : '' }}">
                    <div class="flex flex-row gap-2 items-center p-2">
                        <x-heroicon-o-shopping-cart class="w-6 h-6"/>
                        <span class="">Agendamentos </span>
                    </div>
                </a>

                <a href="#" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('home') && !request('status') ? 'bg-blue-link' : '' }}">
                    <div class="flex flex-row gap-2 items-center p-2">
                        <x-heroicon-o-shopping-cart class="w-6 h-6"/>
                        <span class="">Produtos </span>
                    </div>
                </a>

                <a href="#" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('home') && !request('status') ? 'bg-blue-link' : '' }}">
                    <div class="flex flex-row gap-2 items-center p-2">
                        <x-heroicon-o-tag class="w-6 h-6"/>
                        <span class="">Procedimentos </span>
                    </div>
                </a>

                <a href="#" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('home') && !request('status') ? 'bg-blue-link' : '' }}">
                    <div class="flex flex-row gap-2 items-center p-2">
                        <x-heroicon-o-shopping-cart class="w-6 h-6"/>
                        <span class="">Clientes </span>
                    </div>
                </a>


                <a href="#" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('home') && !request('status') ? 'bg-blue-link' : '' }}">
                    <div class="flex flex-row gap-2 items-center p-2">
                        <x-heroicon-o-shopping-cart class="w-6 h-6"/>
                        <span class="">Relatorios (opcao) </span>
                    </div>
                </a>

            <h1>Fincanceiro abaixo</h1>
                <a href="#" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('home') && !request('status') ? 'bg-blue-link' : '' }}">
                    <div class="flex flex-row gap-2 items-center p-2">
                        <x-heroicon-o-shopping-cart class="w-6 h-6"/>
                        <span class="">Despesas </span>
                    </div>
                </a>
                <a href="#" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('home') && !request('status') ? 'bg-blue-link' : '' }}">
                    <div class="flex flex-row gap-2 items-center p-2">
                        <x-heroicon-o-shopping-cart class="w-6 h-6"/>
                        <span class="">Orçamentos </span>
                    </div>
                </a>




            <a href="{{ route('users') }}" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('users') ? 'bg-blue-link' : '' }}">
                <div class="flex flex-row gap-2 items-center p-2">
                    <x-heroicon-o-tag class="w-6 h-6"/>
                    <span class="">Usuários </span>
                </div>
            </a>

                <a href="{{ route('tenants') }}" wire:navigate class="text-white rounded-md hover:bg-blue-link transition-all duration-200 {{ request()->routeIs('tenants') ? 'bg-blue-link' : '' }}">
                    <div class="flex flex-row gap-2 items-center p-2">
                        <x-heroicon-o-tag class="w-6 h-6"/>
                        <span class="">Tenants </span>
                    </div>
                </a>
{{--            <div--}}
{{--                x-cloak--}}
{{--                x-data="{ open: {{ request()->routeIs(['home']) ? 'true' : 'false' }} }">--}}
{{--                <button @click="open = !open" type="button"--}}
{{--                        class="w-full flex flex-row items-center gap-1 text-white rounded-md p-3 px-4 bg-blue-link transition-all duration-200"--}}
{{--                        :class="{ ' bg-blue-link ': open }">--}}
{{--                    <img src="" class="w-6 h-6" alt="icon_dash"/>--}}
{{--                    <span class="flex-1  text-left rtl:text-right whitespace-nowrap">DROPDOWN</span>--}}
{{--                    <svg class="w-3 h-3 transition-transform" :class="{ 'transform rotate-180': open }"--}}
{{--                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">--}}
{{--                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"--}}
{{--                              stroke-width="2" d="m1 1 4 4 4-4"/>--}}
{{--                    </svg>--}}
{{--                </button>--}}

{{--                <div x-ref="submenu" x-show="open" x-transition x-collapse class="overflow-hidden">--}}
{{--                    <a href="{{ route('home') }}" wire:navigate--}}
{{--                       class="flex flex-row items-center gap-2 text-white rounded-md p-3 px-4 mt-1 bg-blue-link transition-all duration-200 {{ request()->routeIs('home') ? ' bg-blue-link ' : '' }}">--}}
{{--                        <span class="text-[14.5px]">SubLinkssss</span>--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}


        </div>
    </div>


    <div x-data="{ open: false }" class="relative mx-2 max-lg:pb-10">

        <div @click="open = !open"
             class="max-lg:hidden flex flex-row gap-2 items-center cursor-pointer p-1.5 mb-2 rounded-md hover:bg-blue-link transition-all duration-200">
            <div class="bg-gray-100 border border-gray-200 p-1 px-3 rounded-md">
            <span class="w-4 h-4 font-bold text-sm">
                {{ strtoupper(Str::substr(auth()->user()->name, 0, 1)) }}
            </span>
            </div>
            <h1 class="text-white truncate text-sm select-none">{{ auth()->user()->email }}</h1>
            <x-heroicon-o-chevron-up-down class="text-white w-6 h-6" />
        </div>

        <div
            x-cloak
            x-show="open"
            x-transition
            @click.away="open = false"
            class="absolute bottom-full left-0 w-full mb-2 bg-white border border-gray-300 shadow-md rounded-md p-2 z-10">
            <div>
                <div class="border-b border-gray-200">
                    <div class="flex flex-row items-center gap-2 mb-3">
                        <div class="shrink-0 w-7 h-7 p-2 bg-gray-300 rounded-md flex items-center justify-center">
                            <span class="font-medium text-sm">{{ strtoupper(Str::substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="flex flex-col overflow-hidden">
                            <span class="font-semibold text-sm truncate">{{ auth()->user()->name  }}</span>
                            <span class="text-sm text-zinc-500 truncate">{{ auth()->user()->email  }}</span>
                        </div>
                    </div>
                </div>

                <div>
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
    </div>
</div>
