<x-guest-layout>
    <a href="/" class="fixed top-4 left-4 bg-white text-gray-800 font-semibold px-4 py-2 rounded shadow hover:bg-gray-200 transition z-50">
        Terug naar Home
    </a>

    <div class="flex h-screen items-center justify-center">
        <div
            class="relative w-[80vw] h-[100vh] bg-center bg-no-repeat"
            style="background-image: url('/storage/img/explosion.png'); background-size: contain;">

            <!-- Session Status -->
            <x-auth-session-status class="absolute top-[22%] left-1/2 w-80 -translate-x-1/2 mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}"
                  class="absolute top-1/3 left-1/2 w-80 -translate-x-1/2">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                  :value="old('email')" required autofocus autocomplete="username" placeholder="E-mail"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-text-input id="password" class="block mt-1 w-full"
                                  type="password"
                                  name="password"
                                  required autocomplete="current-password" placeholder="Password"/>
                    <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                </div>

                <!-- Remember Me -->
                <div class="block mt-4 text-sm text-gray-200">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                               name="remember">
                        <span class="ms-2">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-300 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                           href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-primary-button class="ms-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>

                <!-- Register Link -->
                <div class="mt-4 text-center">
                    <a class="underline text-sm text-gray-300 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       href="{{ route('register') }}">
                        {{ __("Don't have an account? Register") }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
