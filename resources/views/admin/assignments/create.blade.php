<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.assignments_create_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.assignments.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.class') }}</label>
                        <select name="class_id" id="class_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.assignments_select_class_placeholder') }}</option>
                            @foreach ($classes as $classItem)
                                <option value="{{ $classItem->id }}" {{ old('class_id') == $classItem->id ? 'selected' : '' }}>
                                    {{ $classItem->name }} ({{ $classItem->academicYear->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.subject') }}</label>
                        <select name="subject_id" id="subject_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.assignments_select_class_first_placeholder') }}</option>
                        </select>
                        @error('subject_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.teacher') }}</label>
                        <select name="teacher_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.assignments_select_teacher_placeholder') }}</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->first_name }} {{ $teacher->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.save') }}
                        </button>
                        <a href="{{ route('admin.assignments.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const classSelect = document.getElementById('class_id');
        const subjectSelect = document.getElementById('subject_id');
        const selectSubjectPlaceholder = "{{ addslashes(__('messages.assignments_select_subject_placeholder')) }}";
        const selectClassFirstPlaceholder = "{{ addslashes(__('messages.assignments_select_class_first_placeholder')) }}";

        classSelect.addEventListener('change', function () {
            const classId = this.value;

            if (! classId) {
                subjectSelect.innerHTML = `<option value="">${selectClassFirstPlaceholder}</option>`;
                return;
            }

            fetch('/admin/classes/' + classId + '/subjects-json')
                .then(response => response.json())
                .then(subjects => {
                    subjectSelect.innerHTML = `<option value="">${selectSubjectPlaceholder}</option>`;
                    subjects.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        subjectSelect.appendChild(opt);
                    });
                });
        });
    </script>
</x-app-layout>