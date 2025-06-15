<section class="text-white drop-shadow-lg space-y-6">
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <input
                id="current_password"
                name="current_password"
                type="password"
                placeholder="Huidig wachtwoord"
                autocomplete="current-password"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <input
                id="password"
                name="password"
                type="password"
                placeholder="Nieuw wachtwoord"
                autocomplete="new-password"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                placeholder="Bevestig wachtwoord"
                autocomplete="new-password"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <button type="submit"
                    class="w-full py-3 bg-white text-gray-900 rounded-md hover:bg-gray-300 focus:bg-gray-700 transition font-semibold">
                Wachtwoord wijzigen
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-white mt-2">{{ __('Opgeslagen.') }}</p>
            @endif
        </div>
    </form>
</section>
