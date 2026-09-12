<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.guardians_edit_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('guardian_credentials'))
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-900 rounded-lg">
                    <p class="font-bold mb-2">{{ __('messages.guardian_credentials_flash_title') }}</p>
                    <p>{{ __('messages.guardian_credentials_phone_label') }}: <span dir="ltr" class="font-mono font-bold">{{ session('guardian_credentials')['phone'] }}</span></p>
                    <p>{{ __('messages.guardian_credentials_password_label') }}: <span dir="ltr" class="font-mono font-bold">{{ session('guardian_credentials')['password'] }}</span></p>
                    <p class="text-sm text-blue-700 mt-2">{{ __('messages.guardian_credentials_note') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6">

                <form method="POST" action="{{ route('admin.guardians.update', $guardian) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.name') }} <span class="text-red-600">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $guardian->name) }}"
                               class="w-full border rounded-lg p-2" required>
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_profession_label') }}</label>
                        <input type="text" name="profession" value="{{ old('profession', $guardian->profession) }}"
                               class="w-full border rounded-lg p-2">
                        @error('profession') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.guardians_phone1_required_label') }} <span class="text-red-600">*</span></label>
                        <input type="text" name="phone1" value="{{ old('phone1', $guardian->phone1) }}" dir="ltr"
                               class="w-full border rounded-lg p-2" required>
                        @error('phone1') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.guardians_whatsapp_required_label') }} <span class="text-red-600">*</span></label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $guardian->whatsapp) }}" dir="ltr"
                               class="w-full border rounded-lg p-2" required>
                        @error('whatsapp') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.guardians_phone2_optional_label') }}</label>
                        <input type="text" name="phone2" value="{{ old('phone2', $guardian->phone2) }}" dir="ltr"
                               class="w-full border rounded-lg p-2">
                        @error('phone2') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.guardians_address_label') }}</label>
                        <input type="text" name="address" value="{{ old('address', $guardian->address) }}"
                               class="w-full border rounded-lg p-2">
                        @error('address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            {{ __('messages.update') }}
                        </button>
                        <a href="{{ route('admin.guardians.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>

                @if ($guardian->user)
                    <form method="POST" action="{{ route('admin.guardians.regenerate-password', $guardian) }}"
                          class="mt-4 pt-4 border-t"
                          onsubmit="return confirm('{{ addslashes(__('messages.guardians_regenerate_password_confirm')) }}');">
                        @csrf
                        <button type="submit" class="bg-amber-100 text-amber-800 px-4 py-2 rounded-lg hover:bg-amber-200 text-sm">
                            {{ __('messages.guardians_regenerate_password_button') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>