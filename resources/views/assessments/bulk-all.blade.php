<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.assessments_bulk_all_title') }} — {{ $class->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <p class="text-gray-600 mb-4 text-sm">
                    {{ __('messages.assessments_bulk_all_description') }}
                </p>

                <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
                    <p class="text-sm font-medium mb-2">{{ __('messages.assessments_bulk_all_subjects_label') }}</p>
                    @if ($subjects->isEmpty())
                        <p class="text-sm text-red-600">{{ __('messages.assessments_bulk_all_no_subjects') }}</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($subjects as $subject)
                                <span class="bg-white border rounded-full px-3 py-1 text-xs text-gray-700">{{ $subject->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('assessments.bulk-all.store', $class) }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.academic_year') }}</label>
                        <select name="academic_year_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.select_academic_year_placeholder') }}</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.assessments_tests_per_term_label') }}</label>
                        <input type="number" name="tests_per_term" value="{{ old('tests_per_term', 3) }}"
                               min="1" max="6" class="w-full border rounded p-2" required>
                        @error('tests_per_term') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" {{ $subjects->isEmpty() ? 'disabled' : '' }}
                                class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 disabled:opacity-50">
                            {{ __('messages.assessments_bulk_all_button') }}
                        </button>
                        <a href="{{ route('assessments.class.show', $class) }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
