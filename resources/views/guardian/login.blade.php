<x-guest-layout>
    <div class="mb-4 text-center" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <h1 class="text-xl font-bold text-gray-800">{{ __('messages.parent_login_page_title') }}</h1>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('parent.login') }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        @csrf

        <div>
            <x-input-label for="phone" :value="__('messages.parent_login_phone_label')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" dir="ltr" :value="old('phone')" required autofocus />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('messages.parent_login_password_label')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('messages.auth_remember_me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('messages.parent_login_button') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
