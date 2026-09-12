<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.attendance_monthly_report_title_prefix') }} {{ $class->name }} — <span dir="ltr" style="unicode-bidi: embed;">{{ $month }}/{{ $year }}</span>
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('attendance.report.select') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.attendance_back_to_class_select_link') }}
            </a>

            <div class="bg-white shadow-sm rounded-lg p-4 mb-4">
                <p>
                    {{ __('messages.attendance_expected_sessions_label') }} <strong>{{ $totalExpected }}</strong> —
                    {{ __('messages.attendance_held_sessions_label') }} <strong>{{ $totalHeld }}</strong> —
                    {{ __('messages.attendance_sessions_held_percent_label') }}
                    <strong>{{ $sessionsHeldPercent !== null ? $sessionsHeldPercent.'%' : '-' }}</strong>
                </p>
            </div>

            @if (empty($rows))
                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                    {{ __('messages.attendance_no_students_in_class_empty') }}
                </div>
            @else
                <div class="bg-white shadow-sm rounded-lg p-4 overflow-x-auto">
                    <table class="border-collapse text-sm w-full">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.student') }}</th>
                                @foreach ($subjects as $subject)
                                    <th class="p-2 text-center border-r">{{ $subject->name }}<br><span class="text-xs text-gray-500">{{ __('messages.attendance_absence_hours_suffix') }}</span></th>
                                @endforeach
                                <th class="p-2 text-center border-r">{{ __('messages.attendance_col_total_absence_hours') }}</th>
                                <th class="p-2 text-center border-r">{{ __('messages.attendance_col_attendance_percent') }}</th>
                                <th class="p-2 text-center">{{ __('messages.attendance_col_absence_percent') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2 font-medium whitespace-nowrap">
                                        {{ $row['enrollment']->student->first_name }} {{ $row['enrollment']->student->last_name }}
                                    </td>
                                    @foreach ($subjects as $subject)
                                        <td class="p-2 text-center border-r">{{ $row['subjectData'][$subject->id] ?? 0 }}</td>
                                    @endforeach
                                    <td class="p-2 text-center border-r font-bold">{{ $row['totalAbsenceHours'] }}</td>
                                    <td class="p-2 text-center border-r text-green-600">
                                        {{ $row['attendancePercent'] !== null ? $row['attendancePercent'].'%' : '-' }}
                                    </td>
                                    <td class="p-2 text-center text-red-600">
                                        {{ $row['absencePercent'] !== null ? $row['absencePercent'].'%' : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>