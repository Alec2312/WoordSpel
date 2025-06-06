<x-app-layout>
    @guest
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
            <div class="relative flex h-full w-full">
                <!-- Linker helft rood -->
                <div class="w-1/2 bg-[#FD4D4B] flex items-center justify-center relative pointer-events-none">
                    <!-- Profielfoto links, wél klikbaar -->
                    <img
                        src="{{ Auth::user()->profile_photo_url ?? '/storage/img/default-profile.png' }}"
                        alt="Profielfoto"
                        class="rounded-full border-4 border-white w-36 h-36 object-cover shadow-lg pointer-events-auto"
                    />
                </div>

                <!-- Rechter helft blauw -->
                <div class="w-1/2 bg-[#00BBFC] flex items-center justify-center relative pointer-events-none">
                    <!-- Klikbare profielfoto rechts -->
                    <button>
                        <img
                            src="{{ Auth::user()->profile_photo_url ?? '/storage/img/default-profile.png' }}"
                            alt="Vriend uitnodigen"
                            class="rounded-full border-4 border-white w-36 h-36 object-cover shadow-md cursor-pointer hover:brightness-90 transition"
                        />
                    </button>
                </div>

                <!-- Centrale content (Play knop) -->
                <div class="absolute inset-0 flex items-center justify-center z-10">
                    <a href="{{ route('game') }}" class="pointer-events-auto">
                        <img
                            src="/storage/img/play.png"
                            class="max-h-[60vh] max-w-[60vw] transition hover:brightness-75 cursor-pointer"
                            alt="Play"
                        />
                    </a>
                </div>
            </div>
        @endauth
</x-app-layout>
