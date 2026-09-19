<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
    <head>
        @include('partials.head')
        @vite(['resources/css/home.css'])
    </head>
    <body class="h-full bg-white text-gray-900">
        {{ $slot }}
        
        @fluxScripts
        @livewireScripts
    </body>
</html>
