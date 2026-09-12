<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.grades_entry_title_prefix') }} {{ $assessment->title }} ({{ $assessment->subject->name }} - {{ $assessment->classRoom->name }})
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if ($enrollments->isEmpty())
                    <p class="text-center text-gray-500 py-6">
                        {{ __('messages.grades_no_students_empty') }}
                    </p>
                @else
                    <form method="POST" action="{{ route('grades.store', $assessment) }}">
                        @csrf

                        <table class="w-full text-right border-collapse mb-4">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-2">{{ __('messages.student') }}</th>
                                    <th class="p-2">{{ __('messages.students_number_label') }}</th>
                                    <th class="p-2">{{ __('messages.grades_col_score_out_of_20') }}</th>
                                    <th class="p-2">{{ __('messages.grades_col_absent') }}</th>
                                    <th class="p-2">{{ __('messages.grades_col_teacher_note_optional') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enrollments as $enrollment)
                                    @php
                                        $existingGrade = $grades->get($enrollment->id);
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
                                        <td class="p-2">{{ $enrollment->student->student_number }}</td>
                                        <td class="p-2">
                                            <input type="number" step="0.01" min="0" max="20"
                                                   name="scores[{{ $enrollment->id }}]"
                                                   value="{{ old('scores.'.$enrollment->id, $existingGrade?->score) }}"
                                                   class="w-24 border rounded p-1">
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="checkbox"
                                                   name="absent[{{ $enrollment->id }}]"
                                                   value="1"
                                                   {{ old('absent.'.$enrollment->id, $existingGrade?->is_absent) ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-2">
                                            <input type="text"
                                                   name="notes[{{ $enrollment->id }}]"
                                                   value="{{ old('notes.'.$enrollment->id, $existingGrade?->teacher_note) }}"
                                                   placeholder="{{ __('messages.grades_note_placeholder') }}"
                                                   class="w-full border rounded p-1">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                {{ __('messages.grades_save_button') }}
                            </button>
                            <a href="{{ route('assessments.index') }}"
                               class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                                {{ __('messages.back') }}
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>