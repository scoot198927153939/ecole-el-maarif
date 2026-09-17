<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.guardians_edit_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

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

                    <div class="mb-4">
                        <label class="block font-medium mb-1">
                            {{ __('messages.guardians_password_label') }}
                            @unless ($guardian->user) <span class="text-red-600">*</span> @endunless
                        </label>
                        <input type="text" name="password" dir="ltr"
                               placeholder="{{ $guardian->user ? __('messages.guardians_password_leave_blank_placeholder') : '' }}"
                               class="w-full border rounded-lg p-2" minlength="6" {{ $guardian->user ? '' : 'required' }}>
                        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">
                            {{ __('messages.guardians_password_confirmation_label') }}
                            @unless ($guardian->user) <span class="text-red-600">*</span> @endunless
                        </label>
                        <input type="text" name="password_confirmation" dir="ltr"
                               class="w-full border rounded-lg p-2" minlength="6" {{ $guardian->user ? '' : 'required' }}>
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
            </div>
        </div>
    </div>
</x-app-layout>
