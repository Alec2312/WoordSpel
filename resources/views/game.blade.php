<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connect 4 Speelveld</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://use.fontawesome.com/e81701c933.js"></script>

    {{-- DIT <STYLE> BLOK BLIJFT STAAN ZOALS JE HET WILDE --}}
    <style>
        /* Aangepaste styling voor de Connect 4 spots */
        .spot {
            aspect-ratio: 1 / 1; /* Zorgt ervoor dat de hoogte gelijk is aan de breedte, voor perfecte cirkels */
            border-radius: 9999px; /* Maakt het een perfecte cirkel */
            border: 2px solid rgba(169, 169, 169, 0.5); /* Semi-transparante grijze rand */
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background-color: white; /* Standaard wit voor lege spots */
        }
        /* Kleuren voor de gevulde spots */
        .spot.bg-\[\#007bff\] { /* Blue */
            border-color: #007bff;
        }
        .spot.bg-\[\#dc3545\] { /* Red */
            border-color: #dc3545;
        }
    </style>

</head>
<body class="min-h-screen relative flex flex-col text-center font-sans
    @if ($game->status === 'ongoing')
        {{ $game->current_player_color === 'Blue' ? 'bg-[#007bff]' : 'bg-[#dc3545]' }}
    @else
        {{-- Standaard achtergrondkleur als het spel niet ongoing is --}}
        bg-[#FEDE85]
    @endif
">

<a href="/" class="fixed top-4 left-4 bg-white text-gray-800 font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-gray-100 transition duration-300 ease-in-out z-50">
    Terug naar Home
</a>

<div class="max-w-[960px] mx-auto px-4 flex flex-1 flex-col items-center justify-center gap-8 py-8">

    <div class="flex flex-col md:flex-row items-center justify-center gap-8 md:gap-12 w-full">

        {{-- Profielfoto Links (Speler 1) --}}
        <div class="flex flex-col items-center space-y-4">
            <div class="w-28 h-28 md:w-36 md:h-36 rounded-full border-4 border-blue-500 overflow-hidden shadow-lg flex items-center justify-center">
                <img
                    src="{{ Auth::user()->profile ? asset(Auth::user()->profile) : asset('storage/img/default-profile.png') }}"
                    alt="Profielfoto Links"
                    class="w-full h-full object-cover"
                />
            </div>
            <p class="font-bold text-white text-lg md:text-xl">
                {{ Auth::user()->name ?? 'Speler 1' }} (Blauw)
            </p>
            @auth
                <p class="text-white text-base md:text-lg">
                    Jouw punten: <span id="user-points" class="font-semibold">{{ $currentUser->points }}</span>
                </p>
            @endauth
        </div>

        <div class="relative flex flex-col items-center">
            <div id="column-click-overlay" class="absolute inset-0 grid z-10" style="grid-template-columns: repeat({{ $boardColumns }}, minmax(0, 1fr));">
                @for ($col = 0; $col < $boardColumns; $col++)
                    <form method="POST" action="{{ route('game.move') }}" class="h-full w-full relative flex justify-center items-start pt-2">
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game->id }}">
                        <input type="hidden" name="column" value="{{ $col }}">
                        <button type="submit"
                                class="h-full w-full bg-transparent border-none p-0 cursor-pointer flex justify-center items-start pt-2 group
                                       {{ $game->status !== 'ongoing' ? 'cursor-not-allowed' : 'hover:bg-white/10' }}"
                            {{ $game->status !== 'ongoing' ? 'disabled' : '' }}>
                            <i class="fa fa-arrow-down text-3xl md:text-4xl absolute top-[5px] left-1/2 -translate-x-1/2 transition-opacity duration-200 ease-in-out
                                      {{ $game->current_player_color === 'Blue' ? 'text-[#007bff]' : 'text-[#dc3545]' }}
                                      {{ ($game->status === 'ongoing' ? 'group-hover:opacity-100' : '') . ' opacity-0' }}"></i>
                        </button>
                    </form>
                @endfor
            </div>

            <div id="connect4-board" class="bg-[#4a6b8a] p-6 rounded-xl shadow-xl border-4 border-[#3a5670] z-0">
                <div class="grid gap-3" style="grid-template-columns: repeat({{ $boardColumns }}, minmax(0, 1fr));">
                    @php
                        // BELANGRIJK: Correctie van de array_fill, essentieel voor correcte weergave van het bord
                        $boardState = $game->board_state ?? array_fill(0, $boardRows, array_fill(0, $boardColumns, ''));
                    @endphp

                    @for ($r = $boardRows - 1; $r >= 0; $r--)
                        @for ($c = 0; $c < $boardColumns; $c++)
                            {{-- De 'spot' klasse blijft hier, die wordt gestyled door het <style> blok --}}
                            <div class="spot w-16 h-16 relative
                                        @if(isset($boardState[$r][$c]) && $boardState[$r][$c] === 'Blue') bg-[#007bff] @endif
                                        @if(isset($boardState[$r][$c]) && $boardState[$r][$c] === 'Red') bg-[#dc3545] @endif"
                                 data-row="{{ $r }}" data-col="{{ $c }}">
                            </div>
                        @endfor
                    @endfor
                </div>
            </div>
        </div>

        {{-- Profielfoto Rechts (Tegenspeler) --}}
        <div class="flex flex-col items-center space-y-4">
            <div class="w-28 h-28 md:w-36 md:h-36 rounded-full border-4 border-red-500 overflow-hidden shadow-lg flex items-center justify-center">
                <img
                    src="{{ $opponent->profile ? asset($opponent->profile) : asset('storage/img/default-profile.png') }}"
                    alt="Profielfoto Rechts"
                    class="w-full h-full object-cover"
                />
            </div>
            <p class="font-bold text-white text-lg md:text-xl">{{ $opponent->name }} (Rood)</p>
            <p class="text-white text-base md:text-lg">
                Punten: <span id="opponent-points" class="font-semibold">{{ $opponentTotalScore }}</span>
            </p>
        </div>
    </div>

    <div class="mt-8 text-center flex flex-col sm:flex-row gap-4 justify-center">
        {{-- De "Kies Nieuwe Tegenspeler / Speel Opnieuw" knop blijft, omdat deze de functie heeft om van tegenspeler te wisselen, wat de reset triggert --}}
        <form id="select-opponent-form" method="GET" action="{{ route('game.select-opponent') }}">
            <button type="submit" class="btn bg-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-gray-300 transition duration-300 ease-in-out">
                Kies Nieuwe Tegenspeler
            </button>
        </form>

        {{-- De "Bord Leegmaken" knop blijft --}}
        <form id="clear-board-form" method="POST" action="{{ route('game.clear-board') }}">
            @csrf
            <button type="submit" class="btn bg-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-gray-300 transition duration-300 ease-in-out">
                Bord Leegmaken
            </button>
        </form>

        {{-- De "Reset Scores" knop blijft --}}
        <form id="reset-scores-form" method="POST" action="{{ route('game.reset-scores') }}">
            @csrf
            <button type="submit" class="btn bg-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-gray-300 transition duration-300 ease-in-out">
                Reset Scores
            </button>
        </form>
    </div>

</div>

</body>
</html>
