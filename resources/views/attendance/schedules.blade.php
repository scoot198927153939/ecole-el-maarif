<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.attendance_day_sessions_title_prefix') }} {{ $days[$selectedDay] }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('attendance.select') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.attendance_back_to_day_select_link') }}
            </a>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($schedules->isEmpty())
                    <p class="text-center text-gray-500 py-6">{{ __('messages.attendance_no_sessions_scheduled_empty') }}</p>
                @else
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.session') }}</th>
                                <th class="p-2">{{ __('messages.time') }}</th>
                                <th class="p-2">{{ __('messages.class') }}</th>
                                <th class="p-2">{{ __('messages.subject') }}</th>
                                <th class="p-2">{{ __('messages.teacher') }}</th>
                                <th class="p-2">{{ __('messages.attendance_col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedules as $schedule)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $schedule->session_number }}</td>
                                    <td class="p-2">
                                        <span dir="ltr" style="unicode-bidi: embed;">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="p-2">{{ $schedule->assignment->classRoom->name }}</td>
                                    <td class="p-2">{{ $schedule->assignment->subject->name }}</td>
                                    <td class="p-2">{{ $schedule->assignment->teacher->first_name }} {{ $schedule->assignment->teacher->last_name }}</td>
                                    <td class="p-2">
                                        <form method="GET" action="{{ route('attendance.index', $schedule) }}" class="flex gap-1">
                                            <input type="date" name="date" value="{{ now()->format('Y-m-d') }}"
                                                   class="border rounded p-1 text-sm">
                                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                                {{ __('messages.attendance_take_button') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>