<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.enrollments_create_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.enrollments.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.student') }}</label>
                        <select name="student_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.enrollments_select_student_placeholder') }}</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.enrollments_field_class_label') }}</label>
                        <select name="class_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.enrollments_select_class_placeholder') }}</option>
                            @foreach ($classes as $classItem)
                                <option value="{{ $classItem->id }}" {{ old('class_id') == $classItem->id ? 'selected' : '' }}>
                                    {{ $classItem->name }} ({{ $classItem->grade_level }} - {{ $classItem->academicYear->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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
                        <label class="block font-medium mb-1">{{ __('messages.enrollments_col_enrollment_date') }}</label>
                        <input type="date" name="enrollment_date" value="{{ old('enrollment_date') }}"
                               class="w-full border rounded p-2" required>
                        @error('enrollment_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.enrollments_status_label') }}</label>
                        <select name="enrollment_status" class="w-full border rounded p-2" required>
                            <option value="active" {{ old('enrollment_status', 'active') === 'active' ? 'selected' : '' }}>{{ __('messages.students_status_active') }}</option>
                            <option value="transferred" {{ old('enrollment_status') === 'transferred' ? 'selected' : '' }}>{{ __('messages.enrollments_status_transferred') }}</option>
                            <option value="completed" {{ old('enrollment_status') === 'completed' ? 'selected' : '' }}>{{ __('messages.enrollments_status_completed') }}</option>
                        </select>
                        @error('enrollment_status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.save') }}
                        </button>
                        <a href="{{ route('admin.enrollments.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>