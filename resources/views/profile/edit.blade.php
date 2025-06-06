<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative min-h-screen font-sans antialiased">

<!-- Achtergrond split rood/blauw -->
<div class="fixed inset-0 flex z-0">
    <div class="w-1/2 bg-[#FD4D4B]"></div>
    <div class="w-1/2 bg-[#00BBFC]"></div>
</div>


<!-- Terug knop -->
<a href="/" class="fixed top-4 left-4 z-50 bg-white/90 backdrop-blur-md px-4 py-2 rounded-md font-semibold shadow hover:bg-white transition">
    Terug naar Home
</a>

<!-- Content -->
<main class="relative z-10 flex flex-col items-center justify-start min-h-[2400px] pt-24 px-4 sm:px-6 lg:px-8 space-y-8 mx-auto">

    <header class="text-center">
        <h1 class="text-3xl font-bold text-white drop-shadow-md">Profile</h1>
    </header>

    <!-- Form 1 -->
    <section
        class="relative rounded-xl overflow-hidden w-full min-h-[700px] flex items-center justify-center p-12"
        style="background: url('/storage/img/explosion.png') center center / contain no-repeat;"
    >
        <form class="relative z-10 max-w-md w-80 text-white drop-shadow-lg">
            <input
                id="name"
                type="text"
                placeholder="Jouw naam"
                value="{{ old('name', auth()->user()->name) }}"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 mb-6 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />

            <input
                id="email"
                type="email"
                placeholder="email@voorbeeld.com"
                value="{{ old('name', auth()->user()->email) }}"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 mb-6 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />

            <button
                type="submit"
                class="w-full py-3 bg-white text-gray-900 rounded-md hover:bg-gray-300 focus:bg-gray-700 transition font-semibold"
            >
                Update profiel
            </button>
        </form>
    </section>

    <!-- Form 2 -->
    <section
        class="relative rounded-xl overflow-hidden w-full min-h-[700px] flex items-center justify-center p-12"
        style="background: url('/storage/img/explosion.png') center center / contain no-repeat;"
    >
        <form class="relative z-10 max-w-md w-80 text-white drop-shadow-lg">
            <input
                id="password"
                type="password"
                placeholder="Nieuw wachtwoord"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 mb-6 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />

            <input
                id="password_confirmation"
                type="password"
                placeholder="Bevestig wachtwoord"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 mb-6 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />

            <button
                type="submit"
                class="w-full py-3 bg-white text-gray-900 rounded-md hover:bg-gray-300 focus:bg-gray-700 transition font-semibold"
            >
                Wachtwoord wijzigen
            </button>
        </form>
    </section>

    <!-- Form 3 -->
    <section
        class="relative rounded-xl overflow-hidden w-full min-h-[700px] flex items-center justify-center p-12"
        style="background: url('/storage/img/explosion.png') center center / contain no-repeat;"
    >
        <form class="relative z-10 max-w-md w-80 text-white drop-shadow-lg">
            <p class="mb-6 font-semibold text-lg">
                Wil je je account verwijderen? Dit kan niet ongedaan gemaakt worden.
            </p>
            <button
                type="submit"
                class="w-full py-3 bg-white text-red-600 rounded-md hover:bg-gray-300 focus:bg-gray-700 transition font-bold"
            >
                Account verwijderen
            </button>
        </form>
    </section>

</main>

</body>
</html>
