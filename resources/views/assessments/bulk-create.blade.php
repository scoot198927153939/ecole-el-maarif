<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.assessments_bulk_create_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <p class="text-gray-600 mb-4 text-sm">
                    {{ __('messages.assessments_bulk_create_description') }}
                </p>

                <form method="POST" action="{{ route('assessments.bulk-store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.class') }}</label>
                        <select name="class_id" id="class_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.assignments_select_class_placeholder') }}</option>
                            @foreach ($classes as $classItem)
                                <option value="{{ $classItem->id }}"
                                        data-level="{{ $classLevels[$classItem->id] }}"
                                        {{ old('class_id') == $classItem->id ? 'selected' : '' }}>
                                    {{ $classItem->name }} ({{ $classItem->academicYear->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.subject') }}</label>
                        <select name="subject_id" id="subject_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.assessments_bulk_select_class_first') }}</option>
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
                        <label class="block font-medium mb-1">{{ __('messages.assessments_tests_per_term_label') }}</label>
                        <input type="number" name="tests_per_term" value="{{ old('tests_per_term', 3) }}"
                               min="1" max="6" class="w-full border rounded p-2" required>
                        @error('tests_per_term') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.assessments_bulk_create_all_button') }}
                        </button>
                        <a href="{{ route('assessments.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const allSubjects = @json($subjects->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'grade_level' => $s->grade_level]));

        const classSelect = document.getElementById('class_id');
        const subjectSelect = document.getElementById('subject_id');
        const i18n = {
            selectClassFirst: "{{ addslashes(__('messages.assessments_bulk_select_class_first')) }}",
            noSubjectsForLevel: "{{ addslashes(__('messages.assessments_bulk_no_subjects_for_level')) }}",
            selectSubject: "{{ addslashes(__('messages.assessments_select_subject_placeholder')) }}",
        };

        function updateSubjects() {
            const selectedOption = classSelect.options[classSelect.selectedIndex];
            const level = selectedOption ? selectedOption.getAttribute('data-level') : null;

            subjectSelect.innerHTML = '';

            if (! level) {
                subjectSelect.innerHTML = `<option value="">${i18n.selectClassFirst}</option>`;
                return;
            }

            const filtered = allSubjects.filter(s => s.grade_level === level);

            if (filtered.length === 0) {
                subjectSelect.innerHTML = `<option value="">${i18n.noSubjectsForLevel}</option>`;
                return;
            }

            subjectSelect.innerHTML = `<option value="">${i18n.selectSubject}</option>`;
            filtered.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                subjectSelect.appendChild(opt);
            });
        }

        classSelect.addEventListener('change', updateSubjects);
        updateSubjects();
    </script>
</x-app-layout>