<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connect 4 Speelveld</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://use.fontawesome.com/e81701c933.js"></script>

</head>
<body class="bg-[#FEDE85] min-h-screen relative flex flex-col text-center font-sans">

<a href="/" class="fixed top-4 left-4 bg-white text-gray-800 font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-gray-100 transition duration-300 ease-in-out z-50">
    Terug naar Home
</a>

<div class="max-w-[960px] mx-auto px-4 flex flex-1 flex-col items-center justify-center gap-8 py-8">

    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mt-5 mb-4 drop-shadow-lg">Laravel Connect Four</h1>

    <div class="min-w-[300px] py-3 px-5 rounded-lg border border-transparent text-[#0c5460] bg-[#d1ecf1] border-[#bee5eb] justify-center mt-3 mb-4 mx-auto max-w-lg">
        <div id="game-message" class="text-lg md:text-xl font-semibold">
            @if ($game->message)
                {{ $game->message }}
            @else
                Huidige Beurt: <span id="current-player-display" class="{{ $game->current_player_color === 'Blue' ? 'text-[#007bff]' : 'text-[#dc3545]' }}">{{ $game->current_player_color }}</span>
            @endif
        </div>
        {{-- Foutmeldingen van de sessie --}}
        @if (session('error'))
            <div class="mt-2 text-red-700 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="flex flex-col md:flex-row items-center justify-center gap-8 md:gap-12 w-full">

        <div class="flex flex-col items-center space-y-4">
            <img
                src="{{ Auth::user()->profile_photo_url ?? asset('storage/img/default-profile.png') }}"
                alt="Profielfoto Links"
                class="rounded-full border-4 border-blue-500 w-28 h-28 md:w-36 md:h-36 object-cover shadow-lg"
            />
            <p class="font-bold text-gray-800 text-lg md:text-xl">
                {{ Auth::user()->name ?? 'Speler 1' }} (Blauw)
            </p>
            {{-- Puntenweergave voor de ingelogde gebruiker --}}
            @auth
                <p class="text-gray-700 text-base md:text-lg">
                    Jouw punten: <span id="user-points" class="font-semibold text-blue-700">{{ $currentUser->points }}</span>
                </p>
            @endauth
        </div>

        <div class="relative flex flex-col items-center">
            <div id="column-click-overlay" class="absolute inset-0 grid z-10" style="grid-template-columns: repeat({{ \App\Models\Game::COLUMNS }}, minmax(0, 1fr));">
                @for ($col = 0; $col < \App\Models\Game::COLUMNS; $col++)
                    {{-- Elk kolomgebied is nu een formulier dat een POST-request stuurt --}}
                    <form method="POST" action="{{ route('game.move') }}" class="h-full w-full relative flex justify-center items-start pt-2">
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game->id }}">
                        <input type="hidden" name="column" value="{{ $col }}">
                        <button type="submit"
                                class="h-full w-full bg-transparent border-none p-0 cursor-pointer flex justify-center items-start pt-2 group
                                       {{ $game->status !== 'ongoing' ? 'cursor-not-allowed' : 'hover:bg-white/10' }}"
                            {{-- BELANGRIJKE CONTROLE: Dit attribuut moet 'disabled' zijn als het spel niet 'ongoing' is --}}
                            {{ $game->status !== 'ongoing' ? 'disabled' : '' }}>
                            {{-- De pijl die alleen zichtbaar wordt bij hover als het spel bezig is --}}
                            <i class="fa fa-arrow-down text-3xl md:text-4xl absolute top-[5px] left-1/2 -translate-x-1/2 transition-opacity duration-200 ease-in-out
                                      {{ $game->current_player_color === 'Blue' ? 'text-[#007bff]' : 'text-[#dc3545]' }}
                                      {{ ($game->status === 'ongoing' ? 'group-hover:opacity-100' : '') . ' opacity-0' }}"></i>
                        </button>
                    </form>
                @endfor
            </div>

            <div id="connect4-board" class="bg-[#4a6b8a] p-4 rounded-xl shadow-xl border-4 border-[#3a5670] z-0">
                <div class="grid gap-2" style="grid-template-columns: repeat({{ \App\Models\Game::COLUMNS }}, minmax(0, 1fr));">
                    @php
                        // Zorg ervoor dat $game->board_state een array is.
                        // Als het null is, of leeg, initialiseer dan een leeg bord.
                        $boardState = $game->board_state ?? array_fill(0, \App\Models\Game::ROWS, array_fill(0, \App\Models\Game::COLUMNS, ''));
                    @endphp

                    @for ($r = \App\Models\Game::ROWS - 1; $r >= 0; $r--)
                        @for ($c = 0; $c < \App\Models\Game::COLUMNS; $c++)
                            <div class="spot w-20 h-20 rounded-full border-2 border-gray-400/50 flex items-center justify-center relative
                                        @if(isset($boardState[$r][$c]) && $boardState[$r][$c] === 'Blue') bg-[#007bff] border-[#007bff] @endif
                                        @if(isset($boardState[$r][$c]) && $boardState[$r][$c] === 'Red') bg-[#dc3545] border-[#dc3545] @endif"
                                 data-row="{{ $r }}" data-col="{{ $c }}">
                            </div>
                        @endfor
                    @endfor
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center space-y-4">
            <img
                src="{{ asset('storage/img/default-profile.png') }}"
                alt="Profielfoto Rechts"
                class="rounded-full border-4 border-red-500 w-28 h-28 md:w-36 md:h-36 object-cover shadow-lg"
            />
            <p class="font-bold text-gray-800 text-lg md:text-xl">Speler 2 (Rood)</p>
            {{-- Puntenweergave voor de gastspeler --}}
            <p class="text-gray-700 text-base md:text-lg">
                Score: <span id="guest-player-score" class="font-semibold text-red-700">{{ $guestTotalScore }}</span> {{-- AANGEPAST --}}
            </p>
        </div>
    </div> {{-- Einde profiel en speelveld container --}}


    <div class="mt-8 text-center">
        <form id="restart-game-form" method="GET" action="{{ route('game.restart') }}">
            <button type="submit" class="btn bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 ease-in-out">
                Start Nieuw Spel
            </button>
        </form>
    </div>

</div> {{-- Einde hoofd container --}}

</body>
</html>
