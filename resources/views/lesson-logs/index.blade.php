<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.card_lesson_logs_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.lesson_logs_subject_pdf_title') }}</h3>

                <form method="GET" action="{{ route('lesson-logs.index') }}" class="flex flex-wrap gap-2 items-center mb-4">
                    <select name="class_id" class="border-gray-300 rounded">
                        <option value="">{{ __('messages.lesson_logs_select_class_label') }}</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($selectedClass && $selectedClass->id === $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
                        {{ __('messages.lesson_logs_show_subjects_button') }}
                    </button>
                </form>

                @if ($selectedClass)
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.subject') }}</th>
                                <th class="p-2">{{ __('messages.teacher') }}</th>
                                <th class="p-2">{{ __('messages.lesson_logs_subject_count_col') }}</th>
                                <th class="p-2">{{ __('messages.lesson_logs_col_file') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subjectAssignments as $assignment)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $assignment->subject->name }}</td>
                                    <td class="p-2">{{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}</td>
                                    <td class="p-2">{{ $assignment->lesson_logs_count }}</td>
                                    <td class="p-2">
                                        @if ($assignment->lesson_logs_count > 0)
                                            <a href="{{ route('lesson-logs.pdf', $assignment) }}"
                                               class="text-blue-600 hover:underline">{{ __('messages.lesson_logs_download_subject_pdf') }}</a>
                                        @else
                                            {{ __('messages.lesson_logs_subject_no_lessons') }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">
                                        {{ __('messages.lesson_logs_no_subjects_in_class') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">{{ __('messages.lesson_logs_pick_class_hint') }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.lesson_logs_list_title') }}</h3>
                    <a href="{{ route('lesson-logs.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ __('messages.lesson_logs_add_button') }}
                    </a>
                </div>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.date') }}</th>
                            <th class="p-2">{{ __('messages.title') }}</th>
                            <th class="p-2">{{ __('messages.subject') }}</th>
                            <th class="p-2">{{ __('messages.class') }}</th>
                            <th class="p-2">{{ __('messages.teacher') }}</th>
                            <th class="p-2">{{ __('messages.lesson_logs_col_file') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lessonLogs as $log)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $log->lesson_date->format('Y-m-d') }}</td>
                                <td class="p-2">{{ $log->title }}</td>
                                <td class="p-2">{{ $log->assignment->subject->name }}</td>
                                <td class="p-2">{{ $log->assignment->classRoom->name }}</td>
                                <td class="p-2">{{ $log->assignment->teacher->first_name }} {{ $log->assignment->teacher->last_name }}</td>
                                <td class="p-2">
                                    @if ($log->pdf_path)
                                        <a href="{{ asset('storage/'.$log->pdf_path) }}" target="_blank"
                                           class="text-blue-600 hover:underline">{{ __('messages.lesson_logs_view_pdf_link') }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('lesson-logs.edit', $log) }}"
                                       class="text-green-600 hover:underline">{{ __('messages.lesson_logs_edit_continue_link') }}</a>
                                    <form action="{{ route('lesson-logs.destroy', $log) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('{{ addslashes(__('messages.lesson_logs_delete_confirm')) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-gray-500">
                                    {{ __('messages.lesson_logs_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>