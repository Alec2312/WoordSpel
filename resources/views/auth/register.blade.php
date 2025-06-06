<x-guest-layout>
    <a href="/" class="fixed top-4 left-4 bg-white text-gray-800 font-semibold px-4 py-2 rounded shadow hover:bg-gray-200 transition z-50">
        Terug naar Home
    </a>

    <div class="flex h-screen items-center justify-center">
        <div
            class="relative w-[80vw] h-[100vh] bg-center bg-no-repeat"
            style="background-image: url('/storage/img/explosion.png'); background-size: contain;">
            <form method="POST" action="{{ route('register') }}"
                  class="absolute top-1/3 left-1/2 w-80 -translate-x-1/2">
                @csrf

                <!-- Name -->
                <div>
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                                  required autofocus autocomplete="name" placeholder="Name"/>
                    <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                                  required autocomplete="username" placeholder="E-mail"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-text-input id="password" class="block mt-1 w-full"
                                  type="password"
                                  name="password"
                                  required autocomplete="new-password" placeholder="Password"/>
                    <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                  type="password"
                                  name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password"/>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-300 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-primary-button class="ms-4">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
