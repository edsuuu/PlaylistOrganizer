<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-behavior: smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-container-spotify">

{{--@include('layout.navbar-auth-mobile')--}}
{{--@livewire('modal')--}}
{{--<div class="flex flex-col lg:flex-row">--}}
{{--    @include('layout.navbar-auth-desktop')--}}
{{--    <main class="flex-1 lg:pl-64">--}}
{{--        <div class="p-6">--}}
{{--            {{ $slot }}--}}
{{--        </div>--}}
{{--    </main>--}}
{{--</div>--}}

<main>
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>
