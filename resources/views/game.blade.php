<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connect 4 speelveld</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FEDE85] min-h-screen relative flex flex-col">

<!-- Knop helemaal linksboven -->
<a href="/" class="fixed top-4 left-4 bg-white text-gray-800 font-semibold px-4 py-2 rounded shadow hover:bg-gray-200 transition z-50">
    Terug naar Home
</a>

<!-- Container voor profiel en speelveld -->
<div class="flex flex-1 items-center justify-center gap-12 px-8">

    <!-- Linker profiel -->
    @auth
        <div class="flex flex-col items-center space-y-4">
            <img
                src="{{ Auth::user()->profile_photo_url ?? '/storage/img/default-profile.png' }}"
                alt="Profielfoto Links"
                class="rounded-full border-4 border-white w-36 h-36 object-cover shadow-lg"
            />
            <p class="font-semibold text-gray-800">Speler 1</p>
        </div>
    @else
        <div class="flex flex-col items-center space-y-4">
            <img
                src="/storage/img/default-profile.png"
                alt="Profielfoto Links"
                class="rounded-full border-4 border-white w-36 h-36 object-cover shadow-lg"
            />
            <p class="font-semibold text-gray-800">Speler 1</p>
        </div>
    @endauth

    <!-- Speelveld -->
    <div class="bg-[#808080] p-6 rounded-lg shadow-lg">
        <div class="grid grid-cols-7 grid-rows-6 gap-4">
            @for ($i = 0; $i < 42; $i++)
                <div class="w-20 h-20 rounded-full border-2 border-gray-400 bg-white"></div>
            @endfor
        </div>
    </div>

    <!-- Rechter profiel -->
    @auth
        <div class="flex flex-col items-center space-y-4">
            <button>
                <img
                    src="{{ Auth::user()->profile_photo_url ?? '/storage/img/default-profile.png' }}"
                    alt="Profielfoto Rechts"
                    class="rounded-full border-4 border-white w-36 h-36 object-cover shadow-md cursor-pointer hover:brightness-90 transition"
                />
            </button>
            <p class="font-semibold text-gray-800">Speler 2</p>
        </div>
    @else
        <div class="flex flex-col items-center space-y-4">
            <img
                src="/storage/img/default-profile.png"
                alt="Profielfoto Rechts"
                class="rounded-full border-4 border-white w-36 h-36 object-cover shadow-md"
            />
            <p class="font-semibold text-gray-800">Speler 2</p>
        </div>
    @endauth

</div>

</body>
</html>
