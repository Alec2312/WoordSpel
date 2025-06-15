<section class="text-white drop-shadow-lg space-y-6 text-center">
    <p class="font-semibold text-lg">
        Wil je je account verwijderen? Dit kan niet ongedaan gemaakt worden.
    </p>
    <script src="https://unpkg.com/alpinejs" defer></script>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        Verwijder account
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900 mb-4">
                Weet je zeker dat je je account wilt verwijderen?
            </h2>

            <p class="text-sm text-gray-600 mb-4">
                Dit kan niet ongedaan worden gemaakt. Vul je wachtwoord in om te bevestigen.
            </p>

            <input
                id="password"
                name="password"
                type="password"
                placeholder="Wachtwoord"
                class="text-gray-900 placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C] w-full p-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none mb-6"
            />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />

            <div class="flex justify-end gap-4">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Annuleer
                </x-secondary-button>

                <x-danger-button class="bg-white text-red-600 hover:bg-gray-300 focus:bg-gray-700">
                    Verwijder account
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
