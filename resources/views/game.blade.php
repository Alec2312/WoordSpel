<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Connect 4 Speelveld</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="min-h-screen relative flex flex-col text-center font-sans bg-[#FEDE85]">

<a href="/"
   class="fixed top-4 left-4 bg-white text-gray-800 font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-gray-100 transition duration-300 ease-in-out z-50">
    Terug naar Home
</a>

<div class="max-w-[960px] mx-auto px-4 flex flex-1 flex-col items-center justify-center gap-8 py-8">

    <div class="flex flex-col md:flex-row items-center justify-center gap-8 md:gap-12 w-full">

        {{-- Profielfoto Links (Speler 1) --}}
        <div class="flex flex-col items-center space-y-4">
            <div
                class="w-28 h-28 md:w-36 md:h-36 rounded-full border-4 border-blue-500 overflow-hidden shadow-lg flex items-center justify-center">
                <img
                    src="{{ Auth::user()->profile ? asset(Auth::user()->profile) : asset('storage/img/default-profile.png') }}"
                    alt="Profielfoto Links"
                    class="w-full h-full object-cover"
                />
            </div>
            <p class="font-bold text-lg md:text-xl">
                {{ Auth::user()->name ?? 'Speler 1' }} (Blauw)
            </p>
            {{-- Opmerking: De $currentUser variabele wordt vanuit de Controller doorgegeven en is altijd beschikbaar op deze pagina. --}}
            <p class="text-base md:text-lg">
                Punten: <span id="user-points" class="font-semibold">{{ $currentUser->points }}</span>
            </p>
        </div>

        <div class="relative flex flex-col items-center">
            <div id="column-click-overlay" class="absolute inset-0 grid z-10"
                 style="grid-template-columns: repeat({{ $boardColumns }}, minmax(0, 1fr));">
                {{-- transparante laag van een grid met hetzelfde aantal vakken als het board voor het klikken. elke cel in de grid bevat een from met een button --}}
                @for ($col = 0; $col < $boardColumns; $col++)
                    <form method="POST" action="{{ route('game.move') }}"
                          class="h-full w-full relative flex justify-center items-start pt-2">
                        {{-- Blade Directive: @csrf. Essentieel voor beveiliging; voegt een CSRF-token toe aan het formulier. --}}
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game->id }}">
                        <input type="hidden" name="column" value="{{ $col }}">
                        <button type="submit"
                                class="h-full w-full bg-transparent border-none p-0 cursor-pointer flex justify-center items-start pt-2 group
                                       {{ $game->status !== 'ongoing' ? 'cursor-not-allowed' : 'hover:bg-white/10' }}"
                            {{ $game->status !== 'ongoing' ? 'disabled' : '' }}>
                            {{-- Inline SVG voor de pijl-naar-beneden. --}}
                            <svg class="absolute top-[5px] left-1/2 -translate-x-1/2 transition-opacity duration-200 ease-in-out
                                        {{ $game->current_player_color === 'Blue' ? 'fill-[#007bff]' : 'fill-[#dc3545]' }}
                                        {{ ($game->status === 'ongoing' ? 'group-hover:opacity-100' : '') . ' opacity-0' }}
                                        w-8 h-8 md:w-9 md:h-9"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path
                                    d="M11.9997 13.1714L16.9495 8.22168L18.3637 9.63589L11.9997 16.0002L5.63574 9.63589L7.04996 8.22168L11.9997 13.1714Z"></path>
                            </svg>
                        </button>
                    </form>
                @endfor
            </div>

            <div id="connect4-board" class="bg-[#4a6b8a] p-6 rounded-xl shadow-xl border-4 border-[#3a5670] z-0">
                <div class="grid gap-3" style="grid-template-columns: repeat({{ $boardColumns }}, minmax(0, 1fr));">
                    {{-- De $boardState variabele wordt nu direct vanuit de Controller meegegeven en is hier beschikbaar. --}}
                    {{-- Hierdoor is het @php blok voor de initialisatie niet meer nodig. --}}

                    {{-- Blade Directive: @for. Genereert de rijen en kolommen voor het speelbord. --}}
                    @for ($r = $boardRows - 1; $r >= 0; $r--)
                        {{-- LET OP: HIER IS HET DOLLARTEKEN VOOR $c TOEGEVOEGD --}}
                        @for ($c = 0; $c < $boardColumns; $c++)
                            {{-- FIX: De @if directives bepalen de kleur van de spot op basis van de $boardState. --}}
                            {{-- De borden zijn nu ook correct: geen rand voor gevulde fiches, grijze rand voor lege fiches. --}}
                            <div class="spot w-16 h-16 relative rounded-full
                                        @if($boardState[$r][$c] === 'Blue')
                                            bg-[#007bff] border-0
                                        @elseif($boardState[$r][$c] === 'Red')
                                            bg-[#dc3545] border-0
                                        @else {{-- Wanneer de spot leeg is --}}
                                            bg-white border-2 border-gray-400/50
                                        @endif"
                                 data-row="{{ $r }}" data-col="{{ $c }}">
                            </div>
                        @endfor
                    @endfor
                </div>
            </div>
        </div>

        {{-- Profielfoto Rechts (Tegenspeler) --}}
        <div class="flex flex-col items-center space-y-4">
            <div
                class="w-28 h-28 md:w-36 md:h-36 rounded-full border-4 border-red-500 overflow-hidden shadow-lg flex items-center justify-center">
                <img
                    src="{{ $opponent->profile ? asset($opponent->profile) : asset('storage/img/default-profile.png') }}"
                    alt="Profielfoto Rechts"
                    class="w-full h-full object-cover"
                />
            </div>
            <p class="font-bold text-lg md:text-xl">{{ $opponent->name }} (Rood)</p>
            <p class="text-base md:text-lg">
                Punten: <span id="opponent-points" class="font-semibold">{{ $opponentTotalScore }}</span>
            </p>
        </div>
    </div>

    <div class="mt-8 text-center flex flex-col sm:flex-row gap-4 justify-center">
        {{-- De formulieren voor deze knoppen zijn functioneel en kunnen niet verder worden ingekort zonder functionaliteit te verliezen of complexiteit toe te voegen. --}}
        <form id="select-opponent-form" method="GET" action="{{ route('game.select-opponent') }}">
            <button type="submit"
                    class="btn bg-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-gray-300 transition duration-300 ease-in-out">
                Kies Nieuwe Tegenspeler
            </button>
        </form>

        <form id="clear-board-form" method="POST" action="{{ route('game.clear-board') }}">
            @csrf
            <button type="submit"
                    class="btn bg-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-gray-300 transition duration-300 ease-in-out">
                Bord Leegmaken
            </button>
        </form>

        <form id="reset-scores-form" method="POST" action="{{ route('game.reset-scores') }}">
            @csrf
            <button type="submit"
                    class="btn bg-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-gray-300 transition duration-300 ease-in-out">
                Reset Scores
            </button>
        </form>
    </div>

</div>

</body>
</html>
