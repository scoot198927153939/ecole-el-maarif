<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.staff_members_edit_title') }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">

                <form method="POST" action="{{ route('admin.staff-members.update', $staffMember) }}"
                      x-data="{ salaryType: '{{ old('salary_type', $staffMember->salary_type) }}' }">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_first_name_label') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $staffMember->first_name) }}" class="w-full border rounded-lg p-2" required>
                        @error('first_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_last_name_label') }}</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $staffMember->last_name) }}" class="w-full border rounded-lg p-2" required>
                        @error('last_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.role') }}</label>
                        <select name="role" class="w-full border rounded-lg p-2" required>
                            <option value="director" {{ old('role', $staffMember->role) === 'director' ? 'selected' : '' }}>{{ __('messages.staff_members_role_director') }}</option>
                            <option value="supervisor" {{ old('role', $staffMember->role) === 'supervisor' ? 'selected' : '' }}>{{ __('messages.staff_members_role_supervisor') }}</option>
                            <option value="accountant" {{ old('role', $staffMember->role) === 'accountant' ? 'selected' : '' }}>{{ __('messages.staff_members_role_accountant') }}</option>
                            <option value="cleaner" {{ old('role', $staffMember->role) === 'cleaner' ? 'selected' : '' }}>{{ __('messages.staff_members_role_cleaner') }}</option>
                            <option value="guard" {{ old('role', $staffMember->role) === 'guard' ? 'selected' : '' }}>{{ __('messages.staff_members_role_guard') }}</option>
                        </select>
                        @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.staff_members_phone_optional_label') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $staffMember->phone) }}" dir="ltr" class="w-full border rounded-lg p-2">
                        @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.staff_members_col_salary_type') }}</label>
                        <select name="salary_type" x-model="salaryType" class="w-full border rounded-lg p-2" required>
                            <option value="fixed">{{ __('messages.staff_members_salary_type_fixed_monthly') }}</option>
                            <option value="hourly">{{ __('messages.staff_members_salary_type_hourly_based') }}</option>
                        </select>
                    </div>

                    <div class="mb-4" x-show="salaryType === 'fixed'">
                        <label class="block font-medium mb-1">{{ __('messages.staff_members_fixed_salary_label') }}</label>
                        <input type="number" step="0.01" name="fixed_salary" value="{{ old('fixed_salary', $staffMember->fixed_salary) }}" class="w-full border rounded-lg p-2">
                        @error('fixed_salary') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4" x-show="salaryType === 'hourly'">
                        <label class="block font-medium mb-1">{{ __('messages.staff_members_hourly_rate_label') }}</label>
                        <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $staffMember->hourly_rate) }}" class="w-full border rounded-lg p-2">
                        @error('hourly_rate') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.status') }}</label>
                        <select name="status" class="w-full border rounded-lg p-2" required>
                            <option value="active" {{ old('status', $staffMember->status) === 'active' ? 'selected' : '' }}>{{ __('messages.students_status_active') }}</option>
                            <option value="inactive" {{ old('status', $staffMember->status) === 'inactive' ? 'selected' : '' }}>{{ __('messages.staff_members_status_inactive') }}</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('messages.update') }}</button>
                        <a href="{{ route('admin.staff-members.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>