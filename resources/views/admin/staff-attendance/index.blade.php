<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.card_staff_attendance_title') }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <form method="GET" action="{{ route('admin.staff-attendance.index') }}" class="mb-4 flex gap-2">
                <input type="date" name="date" value="{{ $date }}" class="border rounded-lg p-2">
                <button type="submit" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">{{ __('messages.staff_attendance_view_this_date_button') }}</button>
            </form>

            <form method="POST" action="{{ route('admin.staff-attendance.store') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="bg-white shadow-sm rounded-xl p-6 mb-6 overflow-x-auto">
                    <h3 class="text-lg font-bold mb-1">{{ __('messages.card_teachers_title') }}</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        {{ __('messages.staff_attendance_standard_times_note') }}
                    </p>
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.name') }}</th>
                                @for ($i = 1; $i <= 3; $i++)
                                    <th class="p-2 text-center border-r" colspan="4">{{ __('messages.session') }} {{ $i }}</th>
                                @endfor
                            </tr>
                            <tr class="border-b bg-gray-50 text-xs">
                                <th class="p-2"></th>
                                @for ($i = 1; $i <= 3; $i++)
                                    <th class="p-1 border-r">{{ __('messages.status') }}</th>
                                    <th class="p-1">{{ __('messages.staff_attendance_col_check_in') }}</th>
                                    <th class="p-1">{{ __('messages.staff_attendance_col_check_out') }}</th>
                                    <th class="p-1">{{ __('messages.class') }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teachers as $teacher)
                                @php
                                    $teacherSessions = $existingSessions->get($teacher->id, collect());
                                    $lateNotes = [];
                                    $earlyNotes = [];
                                    $sessionWord = __('messages.session');
                                    $lateWord = __('messages.teacher_attendance_report_late_word');
                                    $earlyWord = __('messages.teacher_attendance_report_early_word');
                                    $minutesShort = __('messages.minutes_short');
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2 font-medium whitespace-nowrap">{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                    @for ($i = 1; $i <= 3; $i++)
                                        @php
                                            $session = $teacherSessions->get($i);
                                            if ($session && $session->late_minutes > 0) {
                                                $lateNotes[] = "$sessionWord $i: $lateWord {$session->late_minutes} $minutesShort";
                                            }
                                            if ($session && $session->early_leave_minutes > 0) {
                                                $earlyNotes[] = "$sessionWord $i: $earlyWord {$session->early_leave_minutes} $minutesShort";
                                            }
                                        @endphp
                                        <td class="p-1 border-r">
                                            <select name="sessions[{{ $teacher->id }}][{{ $i }}][status]" class="border rounded p-1 text-xs">
                                                <option value="" {{ ! $session?->status ? 'selected' : '' }}>—</option>
                                                <option value="present" {{ $session?->status === 'present' ? 'selected' : '' }}>{{ __('messages.staff_attendance_status_present') }}</option>
                                                <option value="absent" {{ $session?->status === 'absent' ? 'selected' : '' }}>{{ __('messages.staff_attendance_status_absent') }}</option>
                                            </select>
                                        </td>
                                        <td class="p-1">
                                            <input type="time" name="sessions[{{ $teacher->id }}][{{ $i }}][actual_start]"
                                                   value="{{ $session?->actual_start ? \Carbon\Carbon::parse($session->actual_start)->format('H:i') : '' }}"
                                                   class="w-20 border rounded p-1">
                                        </td>
                                        <td class="p-1">
                                            <input type="time" name="sessions[{{ $teacher->id }}][{{ $i }}][actual_end]"
                                                   value="{{ $session?->actual_end ? \Carbon\Carbon::parse($session->actual_end)->format('H:i') : '' }}"
                                                   class="w-20 border rounded p-1">
                                        </td>
                                        <td class="p-1">
                                            <select name="sessions[{{ $teacher->id }}][{{ $i }}][class_id]" class="border rounded p-1 text-xs">
                                                <option value="">—</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->id }}" {{ $session?->class_id == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endfor
                                </tr>
                                @if (count($lateNotes) || count($earlyNotes))
                                    <tr class="bg-amber-50 text-xs">
                                        <td colspan="13" class="p-2 text-amber-700">
                                            {{ __('messages.staff_attendance_note_prefix') }}
                                            {{ implode(' — ', array_merge($lateNotes, $earlyNotes)) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.staff_attendance_other_staff_title') }}</h3>
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.name') }}</th>
                                <th class="p-2">{{ __('messages.role') }}</th>
                                <th class="p-2">{{ __('messages.status') }}</th>
                                <th class="p-2">{{ __('messages.staff_attendance_col_hours') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($staffMembers as $staff)
                                @php $existing = $existingStaffAttendance->get($staff->id); @endphp
                                <tr class="border-b">
                                    <td class="p-2">{{ $staff->first_name }} {{ $staff->last_name }}</td>
                                    <td class="p-2">{{ $staff->roleLabel() }}</td>
                                    <td class="p-2">
                                        <select name="staff_status[{{ $staff->id }}]" class="border rounded p-1">
                                            <option value="present" {{ ($existing?->status ?? 'present') === 'present' ? 'selected' : '' }}>{{ __('messages.staff_attendance_status_present') }}</option>
                                            <option value="absent" {{ $existing?->status === 'absent' ? 'selected' : '' }}>{{ __('messages.staff_attendance_status_absent') }}</option>
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.5" name="staff_hours[{{ $staff->id }}]"
                                               value="{{ $existing?->hours }}" class="w-20 border rounded p-1">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    {{ __('messages.staff_attendance_save_button') }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>