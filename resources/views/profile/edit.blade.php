<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile</title>
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

    <!-- Profielinformatie -->
    <section
        class="relative rounded-xl overflow-hidden w-full min-h-[700px] flex items-center justify-center p-12"
        style="background: url('/storage/img/explosion.png') center center / contain no-repeat;"
    >
        <div class="relative z-10 max-w-md w-80 text-white drop-shadow-lg">
            @include('profile.partials.update-profile-information-form', ['user' => $user])
        </div>
    </section>

    <!-- Wachtwoord wijzigen -->
    <section
        class="relative rounded-xl overflow-hidden w-full min-h-[700px] flex items-center justify-center p-12"
        style="background: url('/storage/img/explosion.png') center center / contain no-repeat;"
    >
        <div class="relative z-10 max-w-md w-80 text-white drop-shadow-lg">
            @include('profile.partials.update-password-form')
        </div>
    </section>

    <!-- Profielfoto upload -->
    <section
        class="relative rounded-xl overflow-hidden w-full min-h-[700px] flex items-center justify-center p-12"
        style="background: url('/storage/img/explosion.png') center center / contain no-repeat;"
    >
        <div class="relative z-10 max-w-md w-80 text-white drop-shadow-lg space-y-6 text-center">
            <h2 class="text-xl font-bold">Profielfoto</h2>

            @if ($user->profile)
                <img src="{{ asset($user->profile) }}" class="w-32 h-32 rounded-full object-cover mx-auto mb-4" alt="Profielfoto">
            @endif

            <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <input type="file" name="profile" required class="block w-full text-gray-900 bg-[#FEC70C] border-2 border-black rounded-md p-3 shadow-sm placeholder-black focus:ring-2 focus:ring-yellow-400 focus:outline-none">

                <button type="submit" class="w-full py-3 bg-white text-gray-900 rounded-md hover:bg-gray-300 focus:bg-gray-700 transition font-semibold">
                    Upload
                </button>
            </form>
        </div>
    </section>

    <!-- Account verwijderen -->
    <section
        class="relative rounded-xl overflow-hidden w-full min-h-[700px] flex items-center justify-center p-12"
        style="background: url('/storage/img/explosion.png') center center / contain no-repeat;"
    >
        <div class="relative z-10 max-w-md w-80 text-white drop-shadow-lg text-center space-y-6">

            @include('profile.partials.delete-user-form')
        </div>
    </section>

</main>

</body>
</html>
