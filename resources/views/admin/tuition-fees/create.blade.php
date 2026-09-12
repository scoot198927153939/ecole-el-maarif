<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.tuition_fees_create_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">

                <form method="POST" action="{{ route('admin.tuition-fees.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.grade_level') }}</label>
                        <select name="grade_level" class="w-full border rounded-lg p-2" dir="ltr" required>
                            <option value="">{{ __('messages.tuition_fees_select_level_placeholder') }}</option>
                            @foreach ($gradeLevels as $level)
                                <option value="{{ $level }}" {{ old('grade_level') == $level ? 'selected' : '' }}>
                                    {{ $level }}
                                </option>
                            @endforeach
                        </select>
                        @error('grade_level') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.academic_year') }}</label>
                        <select name="academic_year_id" class="w-full border rounded-lg p-2" required>
                            <option value="">{{ __('messages.tuition_fees_select_generic_placeholder') }}</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.tuition_fees_amount_label') }}</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                               class="w-full border rounded-lg p-2" required>
                        @error('amount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            {{ __('messages.save') }}
                        </button>
                        <a href="{{ route('admin.tuition-fees.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>