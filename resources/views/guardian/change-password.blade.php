<x-guardian-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.guardian_change_password_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('parent.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.guardian_change_password_current_label') }}</label>
                        <input type="password" name="current_password" class="w-full border rounded-lg p-2" required autofocus>
                        @error('current_password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.guardian_change_password_new_label') }}</label>
                        <input type="password" name="password" class="w-full border rounded-lg p-2" required minlength="6">
                        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.guardian_change_password_confirm_label') }}</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded-lg p-2" required minlength="6">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.guardian_change_password_button') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guardian-layout>
