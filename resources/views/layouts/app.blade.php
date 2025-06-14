<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connect 4</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative h-screen font-sans antialiased">

<!-- Achtergrondkleuren -->
<div class="flex h-full w-full absolute inset-0 z-0">
    <div class="w-1/2 bg-[#FD4D4B]"></div>
    <div class="w-1/2 bg-[#00BBFC]"></div>
</div>

<!-- Afbeelding -->
<div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
    @guest
        <img src="/storage/img/welkom.png" alt="Welkom afbeelding" class="h-full object-contain"/>
    @else
        <img src="/storage/img/vs.png" alt="Versus afbeelding" class="h-full object-contain"/>
    @endguest
</div>

<!-- Content -->
<div class="relative z-10 h-full w-full">

    <!-- Navigatie -->
    @include('layouts.navigation')

    <!-- Header -->
    @isset($header)
        <header class="bg-white/70 backdrop-blur-md shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Pagina-inhoud -->
    <main class="flex items-center justify-center min-h-[calc(90vh-65px)]">
        {{ $slot }}
    </main>

</div>

</body>
</html>
