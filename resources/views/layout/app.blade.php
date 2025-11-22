
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="bg-gradient-to-b from-gray-800 to-sidebar-spotify">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <flux:dropdown class="hidden lg:block" position="bottom" align="start" :scroll-lock="false">
        <flux:profile circle :avatar="auth()->user()->spotify->avatar" :name="auth()->user()->name" class="cursor-pointer"/>
        <flux:menu class="w-[220px]">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>

    <flux:navlist variant="outline">

        <flux:navlist.item icon="musical-note" :href="route('dashboard')" :current="request()->routeIs('dashboard')">Dashboard</flux:navlist.item>
        <flux:navlist.item icon="musical-note" :href="route('liked-playlist')" :current="request()->routeIs('liked-playlist')">Músicas curtidas</flux:navlist.item>

        <flux:navlist.group heading="Playlists" class="grid">
            @livewire('sidebar')
        </flux:navlist.group>
    </flux:navlist>
    <flux:spacer/>
</flux:sidebar>

<!-- Mobile User Menu -->
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

    <flux:spacer/>

    <flux:dropdown position="top" align="end">
        <flux:profile circle :avatar="auth()->user()->spotify->avatar" :name="auth()->user()->name" class="cursor-pointer"/>
        <flux:menu>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>

{{ $slot }}

@livewireScripts
@fluxScripts
</body>
</html>
