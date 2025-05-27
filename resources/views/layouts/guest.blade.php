<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative h-screen overflow-hidden">

<div class="flex h-full w-full absolute inset-0">
    <div class="w-1/2 bg-[#FD4D4B]"></div>
    <div class="w-1/2 bg-[#00BBFC]"></div>
</div>

<div class="absolute inset-0 flex items-center justify-center pointer-events-none">
    @guest
        <img src="/storage/img/welkom.png" alt="Welkom afbeelding"
             class="h-full object-contain"/>
    @else
        <img src="/storage/img/vs.png" alt="Versus afbeelding"
             class="h-full object-contain"/>
    @endguest
</div>


<div class="relative z-10 h-full w-full">
    {{ $slot }}
</div>

</body>
</html>
