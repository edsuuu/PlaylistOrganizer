<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-behavior: smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="bg-container-spotify">
<flux:header container class="bg-container-spotify  border-b border-zinc-700">
    {{--    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />--}}

    {{--    <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="{{ config('app.name') }}" class="max-lg:hidden" />--}}


    <a href="{{ route('login') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
        <x-app-logo :name="config('app.name')"/>
    </a>
{{--    <flux:navbar class="-mb-px max-lg:hidden">--}}
{{--        <flux:navbar.item icon="home" href="#" current>Home</flux:navbar.item>--}}
{{--        <flux:navbar.item icon="home" href="#" current>Politica de privacidade</flux:navbar.item>--}}
{{--    </flux:navbar>--}}

    <flux:spacer />

    <flux:dropdown position="top" align="start">
        @auth
            <flux:profile circle :avatar="auth()->user()->spotify->avatar" :name="auth()->user()->name" class="cursor-pointer"/>
        @else
            <flux:profile circle icon="user" class="cursor-pointer"/>
        @endauth

        <flux:menu>
            @auth
                <flux:menu.item :href="route('dashboard')" wire:navigate icon="musical-note" class="cursor-pointer">Dashboard</flux:menu.item>
                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle" class="cursor-pointer">Sair</flux:menu.item>
                </form>
            @else
                <flux:menu.item href="{{ route('spotify-auth') }}" icon="arrow-right-start-on-rectangle"
                                class="cursor-pointer">Login
                </flux:menu.item>
            @endauth

        </flux:menu>
    </flux:dropdown>
</flux:header>

{{--<flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">--}}
{{--    <flux:sidebar.header>--}}
{{--        <flux:sidebar.brand--}}
{{--            href="#"--}}
{{--            logo="https://fluxui.dev/img/demo/dark-mode-logo.png"--}}
{{--            name="{{ config('app.name') }}"--}}
{{--        />--}}
{{--        <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />--}}
{{--    </flux:sidebar.header>--}}
{{--    <flux:sidebar.nav>--}}
{{--        <flux:sidebar.item icon="home" href="#" current>Home</flux:sidebar.item>--}}
{{--        <flux:sidebar.item icon="inbox" badge="12" href="#">Politica de privacidade</flux:sidebar.item>--}}
{{--    </flux:sidebar.nav>--}}
{{--    <flux:sidebar.spacer />--}}
{{--    <flux:sidebar.nav>--}}
{{--        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>--}}
{{--        <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>--}}
{{--    </flux:sidebar.nav>--}}
{{--</flux:sidebar>--}}

<flux:main>
    {{--    @livewire('sidebar')--}}
    {{ $slot }}
</flux:main>


@livewireScripts
@fluxScripts
</body>
</html>
