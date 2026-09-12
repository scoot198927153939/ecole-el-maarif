<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.gradebook_title') }} — {{ $class->name }} — {{ $subject->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('gradebook.subjects', $class) }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.gradebook_back_to_subjects_link') }}
            </a>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($assessments->isEmpty())
                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                    {{ __('messages.gradebook_no_assessments_empty') }}
                    <a href="{{ route('assessments.bulk-create') }}" class="text-blue-600 hover:underline">
                        {{ __('messages.gradebook_create_now_link') }}
                    </a>
                </div>
            @elseif ($enrollments->isEmpty())
                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                    {{ __('messages.attendance_no_students_empty') }}
                </div>
            @else
                <div class="mb-3 flex gap-4 text-sm">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-4 h-4 rounded bg-blue-100 border border-blue-300 inline-block"></span> {{ __('messages.pdf_term1_short') }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-4 h-4 rounded bg-green-100 border border-green-300 inline-block"></span> {{ __('messages.pdf_term2_short') }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-4 h-4 rounded bg-orange-100 border border-orange-300 inline-block"></span> {{ __('messages.pdf_term3_short') }}
                    </span>
                </div>

                <form method="POST" action="{{ route('gradebook.store', [$class, $subject]) }}">
                    @csrf

                    <div class="bg-white shadow-sm rounded-lg p-4 overflow-x-auto">
                        <table class="border-collapse text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-2 bg-gray-50">{{ __('messages.student') }}</th>
                                    @foreach ($assessments as $assessment)
                                        @php
                                            $termBg = match ($assessment->term) {
                                                1 => 'bg-blue-100',
                                                2 => 'bg-green-100',
                                                3 => 'bg-orange-100',
                                                default => 'bg-gray-50',
                                            };
                                        @endphp
                                        <th class="p-2 text-center border-r {{ $termBg }}" colspan="2">
                                            {{ $assessment->title }}<br>
                                            <span class="text-xs text-gray-500">
                                                @if ($assessment->type === 'exam') {{ __('messages.gradebook_exam_type_suffix') }} @endif
                                            </span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enrollments as $enrollment)
                                    @php
                                        $studentGrades = $grades->get($enrollment->id, collect())->keyBy('assessment_id');
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2 font-medium bg-white whitespace-nowrap">
                                            {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
                                        </td>
                                        @foreach ($assessments as $assessment)
                                            @php
                                                $g = $studentGrades->get($assessment->id);
                                                $termBgLight = match ($assessment->term) {
                                                    1 => 'bg-blue-50',
                                                    2 => 'bg-green-50',
                                                    3 => 'bg-orange-50',
                                                    default => '',
                                                };
                                            @endphp
                                            <td class="p-1 border-r {{ $termBgLight }}">
                                                <input type="number" step="0.01" min="0" max="20"
                                                       name="scores[{{ $enrollment->id }}][{{ $assessment->id }}]"
                                                       value="{{ $g?->score }}"
                                                       class="w-16 border rounded p-1 text-center">
                                                <label class="block text-xs text-gray-500 mt-1">
                                                    <input type="checkbox"
                                                           name="absent[{{ $enrollment->id }}][{{ $assessment->id }}]"
                                                           value="1" {{ $g?->is_absent ? 'checked' : '' }}>
                                                    {{ __('messages.staff_attendance_status_absent') }}
                                                </label>
                                            </td>
                                            <td class="p-1 {{ $termBgLight }}">
                                                <input type="text"
                                                       name="notes[{{ $enrollment->id }}][{{ $assessment->id }}]"
                                                       value="{{ $g?->teacher_note }}"
                                                       placeholder="{{ __('messages.gradebook_note_placeholder') }}"
                                                       class="w-24 border rounded p-1 text-xs">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.gradebook_save_all_button') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>