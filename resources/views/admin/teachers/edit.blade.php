<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.teachers_edit_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.name') }}</label>
                        <input type="text" value="{{ $teacher->first_name }} {{ $teacher->last_name }}"
                               class="w-full border rounded p-2 bg-gray-100 text-gray-500" disabled>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.teachers_employee_number_label') }}</label>
                        <input type="text" name="employee_number" value="{{ old('employee_number', $teacher->employee_number) }}"
                               class="w-full border rounded p-2" required>
                        @error('employee_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}"
                               class="w-full border rounded p-2" required>
                        @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $teacher->email) }}"
                               class="w-full border rounded p-2" required>
                        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.specialization') }}</label>
                        <input type="text" name="specialization" value="{{ old('specialization', $teacher->specialization) }}"
                               class="w-full border rounded p-2" required>
                        @error('specialization') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.teachers_hire_date_label') }}</label>
                        <input type="date" name="hire_date"
                               value="{{ old('hire_date', $teacher->hire_date->format('Y-m-d')) }}"
                               class="w-full border rounded p-2" required>
                        @error('hire_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4 bg-blue-50 border border-blue-100 rounded-lg p-4">
                        <label class="block font-medium mb-1">{{ __('messages.staff_members_col_salary_type') }}</label>
                        <select name="salary_type" id="salary-type" class="w-full border rounded p-2" required>
                            <option value="fixed" {{ old('salary_type', $teacher->salary_type) === 'fixed' ? 'selected' : '' }}>{{ __('messages.teachers_salary_fixed_monthly_option') }}</option>
                            <option value="hourly" {{ old('salary_type', $teacher->salary_type) === 'hourly' ? 'selected' : '' }}>{{ __('messages.staff_members_salary_hourly_short') }}</option>
                        </select>
                        @error('salary_type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                        <div id="fixed-field" class="mt-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.staff_members_fixed_salary_label') }}</label>
                            <input type="number" step="0.01" name="fixed_salary"
                                   value="{{ old('fixed_salary', $teacher->fixed_salary) }}"
                                   class="w-full border rounded p-2">
                            @error('fixed_salary') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div id="hourly-field" class="mt-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.teachers_hourly_rate_short_label') }}</label>
                            <input type="number" step="0.01" name="hourly_rate"
                                   value="{{ old('hourly_rate', $teacher->hourly_rate) }}"
                                   class="w-full border rounded p-2">
                            @error('hourly_rate') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_partner" value="1"
                                   {{ old('is_partner', $teacher->is_partner) ? 'checked' : '' }}>
                            <span class="font-medium">{{ __('messages.teachers_is_partner_label') }}</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.status') }}</label>
                        <select name="status" class="w-full border rounded p-2" required>
                            <option value="active" {{ old('status', $teacher->status) === 'active' ? 'selected' : '' }}>{{ __('messages.students_status_active') }}</option>
                            <option value="inactive" {{ old('status', $teacher->status) === 'inactive' ? 'selected' : '' }}>{{ __('messages.staff_members_status_inactive') }}</option>
                        </select>
                        @error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.save') }}
                        </button>
                        <a href="{{ route('admin.teachers.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const salaryType = document.getElementById('salary-type');
        const fixedField = document.getElementById('fixed-field');
        const hourlyField = document.getElementById('hourly-field');

        function toggleFields() {
            if (salaryType.value === 'fixed') {
                fixedField.style.display = 'block';
                hourlyField.style.display = 'none';
            } else {
                fixedField.style.display = 'none';
                hourlyField.style.display = 'block';
            }
        }

        salaryType.addEventListener('change', toggleFields);
        toggleFields();
    </script>
</x-app-layout>