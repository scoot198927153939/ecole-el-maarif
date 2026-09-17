<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.users_edit_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full border rounded p-2" required>
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full border rounded p-2" dir="ltr" required>
                        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.password') }}</label>
                        <input type="text" name="password" dir="ltr" placeholder="{{ __('messages.users_password_leave_blank_placeholder') }}"
                               class="w-full border rounded p-2" minlength="8">
                        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.users_password_confirmation_label') }}</label>
                        <input type="text" name="password_confirmation" dir="ltr"
                               class="w-full border rounded p-2" minlength="8">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.role') }}</label>
                        <select name="role" id="role-select" class="w-full border rounded p-2" required>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>{{ __('messages.users_role_admin') }}</option>
                            <option value="supervisor" {{ old('role', $user->role) === 'supervisor' ? 'selected' : '' }}>{{ __('messages.users_role_supervisor') }}</option>
                            <option value="teacher" {{ old('role', $user->role) === 'teacher' ? 'selected' : '' }}>{{ __('messages.users_role_teacher') }}</option>
                        </select>
                        @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div id="permission-preset-field" class="mb-4">
                        <label class="block font-medium mb-1 text-sm">{{ __('messages.users_permission_preset_label') }}</label>
                        <select id="permission-preset-select" class="w-full border rounded p-2">
                            <option value="">{{ __('messages.users_permission_preset_placeholder') }}</option>
                            @foreach ($permissionPresets as $presetKey => $preset)
                                <option value="{{ $presetKey }}">{{ __('messages.' . $preset['label']) }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.users_permission_preset_hint') }}</p>
                    </div>

                    <div id="permissions-fields" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="font-bold text-sm">{{ __('messages.users_permissions_title') }}</p>
                            <div class="flex gap-3 text-xs">
                                <button type="button" id="permissions-select-all" class="text-blue-600 hover:underline">{{ __('messages.users_permissions_select_all') }}</button>
                                <button type="button" id="permissions-clear-all" class="text-gray-500 hover:underline">{{ __('messages.users_permissions_clear_all') }}</button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">{{ __('messages.users_permissions_hint') }}</p>

                        @php $currentPermissions = old('permissions', $user->permissions ?? []); @endphp
                        @foreach ($moduleGroups as $category => $modules)
                            <div class="mb-3">
                                <p class="text-xs font-bold text-gray-600 mb-1">{{ __('messages.users_permissions_category_' . $category) }}</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach ($modules as $module)
                                        <label class="inline-flex items-center gap-2 text-sm bg-white border rounded p-2">
                                            <input type="checkbox" name="permissions[]" value="{{ $module['key'] }}"
                                                   {{ in_array($module['key'], $currentPermissions) ? 'checked' : '' }}
                                                   class="permission-checkbox">
                                            <span>{{ $module['icon'] }} {{ __('messages.' . $module['label']) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="teaching-permissions-fields" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <p class="font-bold text-sm mb-1">{{ __('messages.users_permissions_category_teaching') }}</p>
                        <p class="text-xs text-gray-500 mb-3">{{ __('messages.users_permissions_teaching_hint') }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($teachingModules as $module)
                                <label class="inline-flex items-center gap-2 text-sm bg-white border rounded p-2">
                                    <input type="checkbox" name="permissions[]" value="{{ $module['key'] }}"
                                           {{ in_array($module['key'], $currentPermissions) ? 'checked' : '' }}
                                           class="permission-checkbox">
                                    <span>{{ $module['icon'] }} {{ __('messages.' . $module['label']) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div id="teacher-fields" class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-4">
                        <p class="font-bold mb-3 text-sm">{{ __('messages.users_teacher_extra_fields_title') }}</p>

                        <div class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.users_phone_number_label') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $teacher->phone ?? '') }}"
                                   class="w-full border rounded p-2">
                            @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.users_specialization_main_subject_label') }}</label>
                            <select name="specialization" class="w-full border rounded p-2">
                                <option value="">{{ __('messages.users_select_subject_placeholder') }}</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject }}" {{ old('specialization', $teacher->specialization ?? '') === $subject ? 'selected' : '' }}>
                                        {{ $subject }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialization') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.teachers_hire_date_label') }}</label>
                            <input type="date" name="hire_date"
                                   value="{{ old('hire_date', isset($teacher) && $teacher->hire_date ? $teacher->hire_date->format('Y-m-d') : '') }}"
                                   class="w-full border rounded p-2">
                            @error('hire_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.staff_members_col_salary_type') }}</label>
                            <select name="salary_type" id="salary-type" class="w-full border rounded p-2">
                                <option value="fixed" {{ old('salary_type', $teacher->salary_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>{{ __('messages.teachers_salary_fixed_monthly_option') }}</option>
                                <option value="hourly" {{ old('salary_type', $teacher->salary_type ?? '') === 'hourly' ? 'selected' : '' }}>{{ __('messages.staff_members_salary_hourly_short') }}</option>
                            </select>
                            @error('salary_type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div id="fixed-field" class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.staff_members_fixed_salary_label') }}</label>
                            <input type="number" step="0.01" name="fixed_salary" value="{{ old('fixed_salary', $teacher->fixed_salary ?? '') }}"
                                   class="w-full border rounded p-2">
                            @error('fixed_salary') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div id="hourly-field" class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.teachers_hourly_rate_short_label') }}</label>
                            <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $teacher->hourly_rate ?? '') }}"
                                   class="w-full border rounded p-2">
                            @error('hourly_rate') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_partner" value="1" {{ old('is_partner', $teacher->is_partner ?? false) ? 'checked' : '' }}>
                            <span class="text-sm">{{ __('messages.users_is_partner_short_label') }}</span>
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.update') }}
                        </button>
                        <a href="{{ route('admin.users.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t">
                    <p class="font-medium mb-1">{{ __('messages.users_send_reset_link_title') }}</p>
                    <p class="text-xs text-gray-500 mb-3">{{ __('messages.users_send_reset_link_hint') }}</p>
                    <form method="POST" action="{{ route('admin.users.send-reset-link', $user) }}">
                        @csrf
                        <button type="submit" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                            {{ __('messages.users_send_reset_link_button') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const roleSelect = document.getElementById('role-select');
        const teacherFields = document.getElementById('teacher-fields');
        const permissionsFields = document.getElementById('permissions-fields');
        const teachingPermissionsFields = document.getElementById('teaching-permissions-fields');
        const permissionPresetField = document.getElementById('permission-preset-field');
        const permissionPresetSelect = document.getElementById('permission-preset-select');
        const permissionPresets = @json($permissionPresets);
        const salaryType = document.getElementById('salary-type');
        const fixedField = document.getElementById('fixed-field');
        const hourlyField = document.getElementById('hourly-field');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        function toggleTeachingPermissionsFields() {
            teachingPermissionsFields.style.display = roleSelect.value === 'supervisor' ? 'block' : 'none';
        }

        function toggleTeacherFields() {
            teacherFields.style.display = roleSelect.value === 'teacher' ? 'block' : 'none';
        }

        function togglePermissionsFields() {
            permissionsFields.style.display = roleSelect.value === 'admin' ? 'none' : 'block';
            permissionPresetField.style.display = roleSelect.value === 'supervisor' ? 'block' : 'none';
        }

        permissionPresetSelect.addEventListener('change', () => {
            const preset = permissionPresets[permissionPresetSelect.value];
            if (! preset) {
                return;
            }
            const keys = new Set([...preset.admin_modules, ...preset.teaching_modules]);
            permissionCheckboxes.forEach(cb => cb.checked = keys.has(cb.value));
        });

        function toggleSalaryFields() {
            fixedField.style.display = salaryType.value === 'fixed' ? 'block' : 'none';
            hourlyField.style.display = salaryType.value === 'hourly' ? 'block' : 'none';
        }

        roleSelect.addEventListener('change', toggleTeacherFields);
        roleSelect.addEventListener('change', togglePermissionsFields);
        roleSelect.addEventListener('change', toggleTeachingPermissionsFields);
        salaryType.addEventListener('change', toggleSalaryFields);

        document.getElementById('permissions-select-all').addEventListener('click', () => {
            permissionCheckboxes.forEach(cb => cb.checked = true);
        });
        document.getElementById('permissions-clear-all').addEventListener('click', () => {
            permissionCheckboxes.forEach(cb => cb.checked = false);
        });

        toggleTeacherFields();
        togglePermissionsFields();
        toggleTeachingPermissionsFields();
        toggleSalaryFields();
    </script>
</x-app-layout>