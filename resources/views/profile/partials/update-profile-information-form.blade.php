<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6 text-white drop-shadow-lg">
        @csrf
        @method('patch')

        <div>
            <input
                id="name"
                name="name"
                type="text"
                placeholder="Jouw naam"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <input
                id="email"
                name="email"
                type="email"
                placeholder="email@voorbeeld.com"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="text-sm mt-2 text-white">
                    {{ __('Je e-mailadres is nog niet geverifieerd.') }}

                    <button form="send-verification" class="underline text-white hover:text-gray-200">
                        {{ __('Klik hier om opnieuw te verzenden.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-300">
                            {{ __('Een nieuwe verificatielink is verstuurd.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <button type="submit"
                    class="w-full py-3 bg-white text-gray-900 rounded-md hover:bg-gray-300 focus:bg-gray-700 transition font-semibold">
                Update profiel
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-white mt-2">{{ __('Opgeslagen.') }}</p>
            @endif
        </div>
    </form>
</section>
