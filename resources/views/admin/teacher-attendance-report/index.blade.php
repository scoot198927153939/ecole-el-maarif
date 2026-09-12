<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.teacher_attendance_report_page_title') }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <form method="GET" class="mb-6 flex gap-2 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('messages.year') }}</label>
                    <input type="number" name="year" value="{{ $year }}" class="border rounded-lg p-2 w-28">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('messages.month') }}</label>
                    <select name="month" class="border rounded-lg p-2">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m === $month ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('messages.view') }}</button>
            </form>

            <div class="bg-white shadow-sm rounded-xl p-6 mb-6 overflow-x-auto">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.teacher_attendance_report_summary_title') }}</h3>
                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.teacher') }}</th>
                            <th class="p-2 text-center">{{ __('messages.teacher_attendance_report_col_present') }}</th>
                            <th class="p-2 text-center">{{ __('messages.teacher_attendance_report_col_absent') }}</th>
                            <th class="p-2 text-center">{{ __('messages.teacher_attendance_report_col_late_count') }}</th>
                            <th class="p-2 text-center">{{ __('messages.teacher_attendance_report_col_late_minutes') }}</th>
                            <th class="p-2 text-center">{{ __('messages.teacher_attendance_report_col_early_count') }}</th>
                            <th class="p-2 text-center">{{ __('messages.teacher_attendance_report_col_early_minutes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2 font-medium">{{ $row['teacher']->first_name }} {{ $row['teacher']->last_name }}</td>
                                <td class="p-2 text-center">{{ $row['present_count'] }}</td>
                                <td class="p-2 text-center text-red-600">{{ $row['absent_count'] }}</td>
                                <td class="p-2 text-center {{ $row['late_count'] > 0 ? 'text-amber-600 font-bold' : '' }}">{{ $row['late_count'] }}</td>
                                <td class="p-2 text-center {{ $row['late_total_minutes'] > 0 ? 'text-amber-600 font-bold' : '' }}">{{ $row['late_total_minutes'] }} {{ __('messages.minutes_short') }}</td>
                                <td class="p-2 text-center {{ $row['early_count'] > 0 ? 'text-amber-600 font-bold' : '' }}">{{ $row['early_count'] }}</td>
                                <td class="p-2 text-center {{ $row['early_total_minutes'] > 0 ? 'text-amber-600 font-bold' : '' }}">{{ $row['early_total_minutes'] }} {{ __('messages.minutes_short') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach ($rows as $row)
                @if ($row['late_incidents']->count() || $row['early_incidents']->count())
                    <div class="bg-white shadow-sm rounded-xl p-6 mb-4">
                        <h4 class="font-bold mb-3">{{ __('messages.teacher_attendance_report_details_prefix') }} {{ $row['teacher']->first_name }} {{ $row['teacher']->last_name }}</h4>

                        @if ($row['late_incidents']->count())
                            <p class="text-sm font-medium text-amber-700 mb-1">{{ __('messages.teacher_attendance_report_late_incidents_title') }}</p>
                            <ul class="text-sm text-gray-600 mb-3 list-disc pr-5">
                                @foreach ($row['late_incidents'] as $incident)
                                    <li>{{ $incident['date'] }} — {{ __('messages.session') }} {{ $incident['session'] }} ({{ $incident['class'] }}) — {{ __('messages.teacher_attendance_report_late_word') }} {{ $incident['minutes'] }} {{ __('messages.minutes_word') }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($row['early_incidents']->count())
                            <p class="text-sm font-medium text-amber-700 mb-1">{{ __('messages.teacher_attendance_report_early_incidents_title') }}</p>
                            <ul class="text-sm text-gray-600 list-disc pr-5">
                                @foreach ($row['early_incidents'] as $incident)
                                    <li>{{ $incident['date'] }} — {{ __('messages.session') }} {{ $incident['session'] }} ({{ $incident['class'] }}) — {{ __('messages.teacher_attendance_report_early_word') }} {{ $incident['minutes'] }} {{ __('messages.minutes_word') }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</x-app-layout>