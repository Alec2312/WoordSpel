<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kies Tegenspeler - Connect 4</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-[#FEDE85] min-h-screen relative flex flex-col items-center justify-center font-sans">

<a href="/" class="fixed top-4 left-4 bg-white text-gray-800 font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-gray-100 transition duration-300 ease-in-out z-50">
    Terug naar Home
</a>

<div class="max-w-md mx-auto p-8 bg-white rounded-xl shadow-lg text-center">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Kies je Tegenspeler</h1>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Fout!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('game.set-opponent') }}">
        @csrf
        <div class="mb-6">
            <label for="opponent_id" class="block text-gray-700 text-lg font-medium mb-3">Selecteer een Speler 2:</label>
            <select name="opponent_id" id="opponent_id" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg">
                <option value="">-- Kies een gebruiker --</option>
                {{-- De 'Gast' optie is verwijderd --}}
                @foreach ($users as $user)
                    {{-- De uitsluiting van Auth::id() is al in de controller gefilterd, maar dubbele check kan geen kwaad --}}
                    @if ($user->id !== Auth::id())
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                @endforeach
            </select>
            @error('opponent_id')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 ease-in-out">
            Start Spel met deze Tegenspeler
        </button>
    </form>
</div>

{{-- Het JavaScript om opponent_type te managen is verwijderd --}}

</body>
</html>
