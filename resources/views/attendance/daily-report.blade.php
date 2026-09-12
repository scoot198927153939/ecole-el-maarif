<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.attendance_daily_report_title_prefix') }} {{ $class->name }} — <span dir="ltr" style="unicode-bidi: embed;">{{ $date->format('Y-m-d') }}</span>
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('attendance.report.select') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.attendance_back_to_class_select_link') }}
            </a>

            @if ($schedules->isEmpty())
                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                    {{ __('messages.attendance_no_sessions_this_weekday_empty') }}
                </div>
            @else
                <div class="bg-white shadow-sm rounded-lg p-4 overflow-x-auto">
                    <table class="border-collapse text-sm w-full">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.student') }}</th>
                                @foreach ($schedules as $schedule)
                                    <th class="p-2 text-center border-r">
                                        {{ $schedule->assignment->subject->name }}<br>
                                        <span class="text-xs text-gray-500">
                                            {{ __('messages.session') }} {{ $schedule->session_number }} —
                                            {{ $schedule->assignment->teacher->first_name }}
                                        </span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enrollments as $enrollment)
                                @php
                                    $studentAttendance = $attendance->get($enrollment->id, collect())->keyBy('schedule_id');
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2 font-medium whitespace-nowrap">
                                        {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
                                    </td>
                                    @foreach ($schedules as $schedule)
                                        @php
                                            $status = $studentAttendance->get($schedule->id)?->status;
                                            $label = match ($status) {
                                                'present' => __('messages.staff_attendance_status_present'),
                                                'absent' => __('messages.staff_attendance_status_absent'),
                                                'late' => __('messages.attendance_status_late'),
                                                'excused' => __('messages.attendance_status_excused'),
                                                default => '-',
                                            };
                                            $color = match ($status) {
                                                'present' => 'text-green-600',
                                                'absent' => 'text-red-600',
                                                'late' => 'text-yellow-600',
                                                'excused' => 'text-blue-600',
                                                default => 'text-gray-400',
                                            };
                                        @endphp
                                        <td class="p-2 text-center border-r {{ $color }}">{{ $label }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>