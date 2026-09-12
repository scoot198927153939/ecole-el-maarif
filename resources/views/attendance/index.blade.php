<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.attendance_take_title_prefix') }} {{ $schedule->assignment->subject->name }} — {{ $schedule->assignment->classRoom->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-4">
                <p>{{ __('messages.attendance_date_label_prefix') }} <span dir="ltr" style="unicode-bidi: embed;">{{ $date }}</span></p>
                <p>{{ __('messages.attendance_teacher_label_prefix') }} {{ $schedule->assignment->teacher->first_name }} {{ $schedule->assignment->teacher->last_name }}</p>
                <p>
                    {{ __('messages.session') }} {{ $schedule->session_number }} —
                    <span dir="ltr" style="unicode-bidi: embed;">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                        -
                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                    </span>
                </p>
            </div>

            @if ($enrollments->isEmpty())
                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                    {{ __('messages.attendance_no_students_empty') }}
                </div>
            @else
                <form method="POST" action="{{ route('attendance.store', $schedule) }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-2">{{ __('messages.student') }}</th>
                                    <th class="p-2 text-center">{{ __('messages.staff_attendance_status_present') }}</th>
                                    <th class="p-2 text-center">{{ __('messages.staff_attendance_status_absent') }}</th>
                                    <th class="p-2 text-center">{{ __('messages.attendance_status_late') }}</th>
                                    <th class="p-2 text-center">{{ __('messages.attendance_status_excused') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enrollments as $enrollment)
                                    @php
                                        $current = $existingAttendance->get($enrollment->id)?->status ?? 'present';
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
                                        <td class="p-2 text-center">
                                            <input type="radio" name="status[{{ $enrollment->id }}]" value="present"
                                                   {{ $current === 'present' ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="radio" name="status[{{ $enrollment->id }}]" value="absent"
                                                   {{ $current === 'absent' ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="radio" name="status[{{ $enrollment->id }}]" value="late"
                                                   {{ $current === 'late' ? 'checked' : '' }}>
                                        </td>
                                        <td class="p-2 text-center">
                                            <input type="radio" name="status[{{ $enrollment->id }}]" value="excused"
                                                   {{ $current === 'excused' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.attendance_save_button') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>