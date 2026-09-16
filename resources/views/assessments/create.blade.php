<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.assessments_create_title') }} — {{ $class->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('assessments.store', $class) }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.assessments_title_label_example') }}</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full border rounded p-2" required>
                        @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.type') }}</label>
                        <select name="type" class="w-full border rounded p-2" required>
                            <option value="test" {{ old('type') === 'test' ? 'selected' : '' }}>{{ __('messages.assessments_type_test') }}</option>
                            <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>{{ __('messages.assessments_type_exam') }}</option>
                        </select>
                        @error('type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.assessments_term_label') }}</label>
                        <select name="term" class="w-full border rounded p-2" required>
                            <option value="1" {{ old('term') == '1' ? 'selected' : '' }}>{{ __('messages.pdf_term1_short') }}</option>
                            <option value="2" {{ old('term') == '2' ? 'selected' : '' }}>{{ __('messages.pdf_term2_short') }}</option>
                            <option value="3" {{ old('term') == '3' ? 'selected' : '' }}>{{ __('messages.pdf_term3_short') }}</option>
                        </select>
                        @error('term') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.subject') }}</label>
                        <select name="subject_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.assessments_select_subject_placeholder') }}</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

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
                        <label class="block font-medium mb-1">{{ __('messages.assessments_coefficient_label_example') }}</label>
                        <input type="number" step="0.1" min="0.1" name="coefficient" value="{{ old('coefficient', 1) }}"
                               class="w-full border rounded p-2" required>
                        @error('coefficient') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.assessments_date_optional_label') }}</label>
                        <input type="date" name="assessment_date" value="{{ old('assessment_date') }}"
                               class="w-full border rounded p-2">
                        @error('assessment_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.save') }}
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