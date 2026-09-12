<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.students_create_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.students.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_number_label') }}</label>
                        <input type="text" value="{{ $nextNumber }}"
                               class="w-full border rounded p-2 bg-gray-100 text-gray-500" disabled>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.students_number_hint') }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_national_id_label') }}</label>
                        <input type="text" name="national_id" value="{{ old('national_id') }}"
                               class="w-full border rounded p-2">
                        @error('national_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_school_number_label') }}</label>
                        <input type="text" name="school_number" value="{{ old('school_number') }}"
                               class="w-full border rounded p-2">
                        @error('school_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_first_name_label') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}"
                               class="w-full border rounded p-2" required>
                        @error('first_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_last_name_label') }}</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                               class="w-full border rounded p-2" required>
                        @error('last_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_birth_date_label') }}</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                               class="w-full border rounded p-2" required>
                        @error('birth_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_birth_place_label') }}</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place') }}"
                               class="w-full border rounded p-2">
                        @error('birth_place') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.students_level_label') }}</label>
                        <select id="level-select" class="w-full border rounded p-2">
                            <option value="">{{ __('messages.students_level_placeholder') }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.class') }}</label>
                        <select name="class_id" id="class-select" class="w-full border rounded p-2" required disabled>
                            <option value="">{{ __('messages.students_class_placeholder_select_level_first') }}</option>
                        </select>
                        @error('class_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.guardian') }}</label>
                        <div class="flex gap-2">
                            <select name="guardian_id" id="guardian-select" class="w-full border rounded p-2">
                                <option value="">{{ __('messages.students_guardian_none_placeholder') }}</option>
                                @foreach ($guardians as $guardian)
                                    <option value="{{ $guardian->id }}" {{ old('guardian_id') == $guardian->id ? 'selected' : '' }}>
                                        {{ $guardian->name }} ({{ $guardian->phone1 }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="open-guardian-modal"
                                    class="bg-gray-200 px-3 py-2 rounded hover:bg-gray-300 whitespace-nowrap">
                                {{ __('messages.students_guardian_add_new_button') }}
                            </button>
                        </div>
                        @error('guardian_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.status') }}</label>
                        <select name="status" class="w-full border rounded p-2" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>{{ __('messages.students_status_active') }}</option>
                            <option value="graduated" {{ old('status') === 'graduated' ? 'selected' : '' }}>{{ __('messages.students_status_graduated') }}</option>
                            <option value="withdrawn" {{ old('status') === 'withdrawn' ? 'selected' : '' }}>{{ __('messages.students_status_withdrawn') }}</option>
                        </select>
                        @error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.save') }}
                        </button>
                        <a href="{{ route('admin.students.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="guardian-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="font-bold text-lg mb-4">{{ __('messages.students_guardian_modal_title') }}</h3>

            <div id="guardian-modal-error" class="hidden mb-3 p-2 bg-red-100 text-red-700 rounded text-sm"></div>

            <div class="mb-3">
                <label class="block font-medium mb-1 text-sm">{{ __('messages.name') }}</label>
                <input type="text" id="gm-name" class="w-full border rounded p-2">
            </div>
            <div class="mb-3">
                <label class="block font-medium mb-1 text-sm">{{ __('messages.students_profession_label') }}</label>
                <input type="text" id="gm-profession" class="w-full border rounded p-2">
            </div>
            <div class="mb-3">
                <label class="block font-medium mb-1 text-sm">{{ __('messages.students_col_phone1') }}</label>
                <input type="text" id="gm-phone1" class="w-full border rounded p-2">
            </div>
            <div class="mb-3">
                <label class="block font-medium mb-1 text-sm">{{ __('messages.students_col_whatsapp') }}</label>
                <input type="text" id="gm-whatsapp" class="w-full border rounded p-2">
            </div>
            <div class="mb-3">
                <label class="block font-medium mb-1 text-sm">{{ __('messages.students_phone2_label') }}</label>
                <input type="text" id="gm-phone2" class="w-full border rounded p-2">
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1 text-sm">{{ __('messages.students_address_label') }}</label>
                <input type="text" id="gm-address" class="w-full border rounded p-2">
            </div>

            <div class="flex gap-2 justify-end">
                <button type="button" id="cancel-guardian-modal" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" id="save-guardian-modal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    {{ __('messages.students_guardian_modal_save_button') }}
                </button>
            </div>
        </div>
    </div>

    @php
        $classesForJs = $classes->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'level' => preg_replace('/\d+$/', '', $c->name),
                'year' => optional($c->academicYear)->name ?? '',
            ];
        });
    @endphp

    <script>
        const allClasses = {!! $classesForJs->toJson() !!};
        const i18n = {
            selectLevelFirst: "{{ addslashes(__('messages.students_class_placeholder_select_level_first')) }}",
            selectClass: "{{ addslashes(__('messages.students_class_placeholder_select_class')) }}",
            genericError: "{{ addslashes(__('messages.students_guardian_modal_error_generic')) }}",
            connectionError: "{{ addslashes(__('messages.students_guardian_modal_error_connection')) }}",
        };

        const levelSelect = document.getElementById('level-select');
        const classSelect = document.getElementById('class-select');

        const levels = [...new Set(allClasses.map(c => c.level))].sort();
        levels.forEach(level => {
            const opt = document.createElement('option');
            opt.value = level;
            opt.textContent = level;
            levelSelect.appendChild(opt);
        });

        levelSelect.addEventListener('change', () => {
            const selectedLevel = levelSelect.value;
            classSelect.innerHTML = '';

            if (!selectedLevel) {
                classSelect.innerHTML = `<option value="">${i18n.selectLevelFirst}</option>`;
                classSelect.disabled = true;
                return;
            }

            classSelect.disabled = false;
            classSelect.innerHTML = `<option value="">${i18n.selectClass}</option>`;

            allClasses
                .filter(c => c.level === selectedLevel)
                .forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = `${c.name} (${c.year})`;
                    classSelect.appendChild(opt);
                });
        });

        const modal = document.getElementById('guardian-modal');
        const guardianSelect = document.getElementById('guardian-select');
        const errorBox = document.getElementById('guardian-modal-error');
        const csrfToken = document.querySelector('input[name="_token"]').value;

        document.getElementById('open-guardian-modal').addEventListener('click', () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        document.getElementById('cancel-guardian-modal').addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            errorBox.classList.add('hidden');
        });

        document.getElementById('save-guardian-modal').addEventListener('click', async () => {
            errorBox.classList.add('hidden');

            const payload = {
                name: document.getElementById('gm-name').value,
                profession: document.getElementById('gm-profession').value,
                phone1: document.getElementById('gm-phone1').value,
                whatsapp: document.getElementById('gm-whatsapp').value,
                phone2: document.getElementById('gm-phone2').value,
                address: document.getElementById('gm-address').value,
            };

            try {
                const response = await fetch('{{ route('admin.guardians.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    const errData = await response.json();
                    const messages = errData.errors ? Object.values(errData.errors).flat().join(' ') : i18n.genericError;
                    errorBox.textContent = messages;
                    errorBox.classList.remove('hidden');
                    return;
                }

                const guardian = await response.json();

                const opt = document.createElement('option');
                opt.value = guardian.id;
                opt.textContent = `${guardian.name} (${guardian.phone1})`;
                opt.selected = true;
                guardianSelect.appendChild(opt);

                modal.classList.add('hidden');
                modal.classList.remove('flex');

                ['gm-name', 'gm-profession', 'gm-phone1', 'gm-whatsapp', 'gm-phone2', 'gm-address']
                    .forEach(id => document.getElementById(id).value = '');

            } catch (e) {
                errorBox.textContent = i18n.connectionError;
                errorBox.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>