<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.academic_years_create_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.academic-years.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.academic_years_name_label_example') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full border rounded p-2" required>
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.start_date') }}</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                               class="w-full border rounded p-2" required>
                        @error('start_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.end_date') }}</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full border rounded p-2" required>
                        @error('end_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <input type="checkbox" name="is_current" value="1" id="is_current"
                               {{ old('is_current') ? 'checked' : '' }}>
                        <label for="is_current">{{ __('messages.academic_years_is_current_label') }}</label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.save') }}
                        </button>
                        <a href="{{ route('admin.academic-years.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>