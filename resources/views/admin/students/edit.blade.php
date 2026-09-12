<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.students_edit_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">

                <form method="POST" action="{{ route('admin.students.update', $student) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_number_label') }}</label>
                        <input type="text" value="{{ $student->student_number }}"
                               class="w-full border rounded-lg p-2 bg-gray-100 text-gray-500" disabled>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.students_number_hint') }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_national_id_label') }}</label>
                        <input type="text" name="national_id" value="{{ old('national_id', $student->national_id) }}"
                               class="w-full border rounded-lg p-2">
                        @error('national_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_school_number_label') }}</label>
                        <input type="text" name="school_number" value="{{ old('school_number', $student->school_number) }}"
                               class="w-full border rounded-lg p-2">
                        @error('school_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_first_name_label') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}"
                               class="w-full border rounded-lg p-2" required>
                        @error('first_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_last_name_label') }}</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}"
                               class="w-full border rounded-lg p-2" required>
                        @error('last_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_birth_date_label') }}</label>
                        <input type="date" name="birth_date"
                               value="{{ old('birth_date', $student->birth_date->format('Y-m-d')) }}"
                               class="w-full border rounded-lg p-2" required>
                        @error('birth_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_birth_place_label') }}</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}"
                               class="w-full border rounded-lg p-2">
                        @error('birth_place') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4 bg-blue-50 border border-blue-100 rounded-lg p-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_guardian_edit_label') }}</label>
                        <select name="guardian_id" class="w-full border rounded-lg p-2">
                            <option value="">{{ __('messages.students_guardian_none_placeholder_edit') }}</option>
                            @foreach ($guardians as $guardian)
                                <option value="{{ $guardian->id }}"
                                    {{ old('guardian_id', $student->guardian_id) == $guardian->id ? 'selected' : '' }}>
                                    {{ $guardian->name }} ({{ $guardian->phone1 }})
                                </option>
                            @endforeach
                        </select>
                        @error('guardian_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-2">
                            {{ __('messages.students_guardian_not_found_hint') }}
                            <a href="{{ route('admin.guardians.create') }}" target="_blank" class="text-blue-600 hover:underline">{{ __('messages.students_guardian_add_now_link') }}</a>
                            {{ __('messages.students_guardian_then_return') }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.status') }}</label>
                        <select name="status" class="w-full border rounded-lg p-2" required>
                            <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>{{ __('messages.students_status_active') }}</option>
                            <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>{{ __('messages.students_status_graduated') }}</option>
                            <option value="withdrawn" {{ old('status', $student->status) === 'withdrawn' ? 'selected' : '' }}>{{ __('messages.students_status_withdrawn') }}</option>
                        </select>
                        @error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            {{ __('messages.update') }}
                        </button>
                        <a href="{{ route('admin.students.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>