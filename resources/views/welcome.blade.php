<x-app-layout>
    @guest
        {{-- Voor gastgebruikers: knoppen onderaan gecentreerd --}}
        <div class="absolute bottom-8 left-0 w-full flex justify-center z-20">
            <div class="flex space-x-4">
                <a href="{{ route('register') }}"
                   class="bg-white px-4 py-2 rounded-md font-semibold transition hover:bg-gray-300">Register</a>
                <a href="{{ route('login') }}"
                   class="bg-white px-4 py-2 rounded-md font-semibold transition hover:bg-gray-300">Login</a>
            </div>
        </div>
    @endguest

    @auth
        {{-- Voor ingelogde gebruikers: een layout met drie kolommen --}}
        <div class="flex h-full w-full"> {{-- Hoofdcontainer vult de beschikbare ruimte --}}
            <!-- Linker kolom: Profiel -->
            <div class="w-1/3 bg-[#FD4D4B] flex items-center justify-center"> {{-- Neemt 1/3 van de breedte in --}}
                <a href="/profile">
                    <img
                        src="/storage/img/profile.png"
                        alt="Profielfoto"
                        class="rounded-full w-48 h-48 object-cover shadow-lg cursor-pointer transition hover:brightness-90"
                    />
                </a>
            </div>

            <!-- Midden kolom: Play knop -->
            <div class="w-1/3 flex items-center justify-center"> {{-- Neemt 1/3 van de breedte in --}}
                <a href="{{ route('game') }}">
                    <img
                        src="/storage/img/play.png"
                        class="max-h-[60vh] max-w-[60vw] transition hover:brightness-75 cursor-pointer"
                        alt="Play"
                    />
                </a>
            </div>

            <!-- Rechter kolom: Vriend uitnodigen -->
            <div class="w-1/3 bg-[#00BBFC] flex items-center justify-center"> {{-- Neemt 1/3 van de breedte in --}}
                <a href="/">
                    <img
                        src="/storage/img/profile-plus.png"
                        alt="Vriend uitnodigen"
                        class="rounded-full w-48 h-48 object-cover shadow-md transition hover:brightness-90 cursor-pointer"
                    />
                </a>
            </div>
        </div>
    @endauth
</x-app-layout>
