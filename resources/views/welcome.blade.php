<x-guest-layout>
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
</x-guest-layout>
