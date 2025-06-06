<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Profielinformatie --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Wachtwoord wijzigen --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Profielfoto upload --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl space-y-4">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Profielfoto') }}
                    </h2>

                    @php /** @var \App\Models\User|null $user */ $user = auth()->user(); @endphp

                    @if ($user && $user->profile)
                        <img src="{{ asset($user->profile) }}" alt="Profielafbeelding" class="w-32 h-32 rounded-full object-cover mb-4">
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <label class="block mb-2 font-semibold text-gray-700" for="profile">
                            Upload nieuwe profielfoto:
                        </label>
                        <input type="file" name="profile" id="profile" class="block mb-4">

                        <x-primary-button class="mt-2">
                            {{ __('Upload') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>

            {{-- Account verwijderen --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
