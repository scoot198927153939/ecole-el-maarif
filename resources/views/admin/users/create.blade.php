<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.users_create_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full border rounded p-2" required>
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" dir="ltr"
                               class="w-full border rounded p-2" required>
                        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded text-sm text-blue-800">
                        {{ __('messages.users_password_invite_note') }}
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.role') }}</label>
                        <select name="role" id="role-select" class="w-full border rounded p-2" required>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('messages.users_role_admin') }}</option>
                            <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>{{ __('messages.users_role_supervisor') }}</option>
                            <option value="teacher" {{ old('role', 'teacher') === 'teacher' ? 'selected' : '' }}>{{ __('messages.users_role_teacher') }}</option>
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

                        @foreach ($moduleGroups as $category => $modules)
                            <div class="mb-3">
                                <p class="text-xs font-bold text-gray-600 mb-1">{{ __('messages.users_permissions_category_' . $category) }}</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach ($modules as $module)
                                        <label class="inline-flex items-center gap-2 text-sm bg-white border rounded p-2">
                                            <input type="checkbox" name="permissions[]" value="{{ $module['key'] }}"
                                                   {{ in_array($module['key'], old('permissions', [])) ? 'checked' : '' }}
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
                                           {{ in_array($module['key'], old('permissions', [])) ? 'checked' : '' }}
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
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full border rounded p-2">
                            @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.users_specialization_main_subject_label') }}</label>
                            <select name="specialization" class="w-full border rounded p-2">
                                <option value="">{{ __('messages.users_select_subject_placeholder') }}</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject }}" {{ old('specialization') === $subject ? 'selected' : '' }}>
                                        {{ $subject }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialization') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.teachers_hire_date_label') }}</label>
                            <input type="date" name="hire_date" value="{{ old('hire_date') }}"
                                   class="w-full border rounded p-2">
                            @error('hire_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.staff_members_col_salary_type') }}</label>
                            <select name="salary_type" id="salary-type" class="w-full border rounded p-2">
                                <option value="fixed" {{ old('salary_type', 'fixed') === 'fixed' ? 'selected' : '' }}>{{ __('messages.teachers_salary_fixed_monthly_option') }}</option>
                                <option value="hourly" {{ old('salary_type') === 'hourly' ? 'selected' : '' }}>{{ __('messages.staff_members_salary_hourly_short') }}</option>
                            </select>
                            @error('salary_type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div id="fixed-field" class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.staff_members_fixed_salary_label') }}</label>
                            <input type="number" step="0.01" name="fixed_salary" value="{{ old('fixed_salary') }}"
                                   class="w-full border rounded p-2">
                            @error('fixed_salary') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div id="hourly-field" class="mb-3">
                            <label class="block font-medium mb-1 text-sm">{{ __('messages.teachers_hourly_rate_short_label') }}</label>
                            <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate') }}"
                                   class="w-full border rounded p-2">
                            @error('hourly_rate') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_partner" value="1" {{ old('is_partner') ? 'checked' : '' }}>
                            <span class="text-sm">{{ __('messages.users_is_partner_short_label') }}</span>
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.save') }}
                        </button>
                        <a href="{{ route('admin.users.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
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

        function toggleTeacherFields() {
            teacherFields.style.display = roleSelect.value === 'teacher' ? 'block' : 'none';
        }

        function toggleTeachingPermissionsFields() {
            teachingPermissionsFields.style.display = roleSelect.value === 'supervisor' ? 'block' : 'none';
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