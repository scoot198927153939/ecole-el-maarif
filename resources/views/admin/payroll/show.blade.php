<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.payroll_show_title_prefix') }} {{ $person->first_name }} {{ $person->last_name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ $errors->first() }}</div>
            @endif

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

            <div class="bg-white shadow-sm rounded-xl p-6 mb-4">
                <h3 class="font-bold mb-4">{{ __('messages.payroll_performance_data_title') }}</h3>
                <table class="w-full text-right border-collapse text-sm">
                    <tbody>
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50 w-1/2">{{ __('messages.staff_members_col_salary_type') }}</th>
                            <td class="p-2">{{ $person->salary_type === 'fixed' ? __('messages.teachers_salary_fixed_monthly_option') : __('messages.staff_members_salary_hourly_short') }}</td>
                        </tr>
                        @if ($person->salary_type === 'hourly')
                            <tr class="border-b">
                                <th class="p-2 bg-gray-50">{{ __('messages.payroll_hourly_rate_row_label') }}</th>
                                <td class="p-2">{{ number_format($person->hourly_rate, 2) }}</td>
                            </tr>
                            <tr class="border-b">
                                <th class="p-2 bg-gray-50">{{ __('messages.payroll_hours_worked_this_month_label') }}</th>
                                <td class="p-2">{{ $hoursWorked }}</td>
                            </tr>
                        @else
                            <tr class="border-b">
                                <th class="p-2 bg-gray-50">{{ __('messages.staff_members_fixed_salary_label') }}</th>
                                <td class="p-2">{{ number_format($person->fixed_salary, 2) }}</td>
                            </tr>
                        @endif

                        @if ($type === 'teacher')
                            <tr class="border-b">
                                <th class="p-2 bg-gray-50">{{ __('messages.payroll_late_minutes_this_month_label') }}</th>
                                <td class="p-2 {{ $lateMinutes > 0 ? 'text-amber-600 font-bold' : '' }}">{{ $lateMinutes }} {{ __('messages.minutes_word') }}</td>
                            </tr>
                            <tr class="border-b">
                                <th class="p-2 bg-gray-50">{{ __('messages.payroll_scheduled_sessions_per_week_label') }}</th>
                                <td class="p-2">{{ $scheduledSessionsPerWeek }}</td>
                            </tr>
                            <tr class="border-b">
                                <th class="p-2 bg-gray-50">{{ __('messages.payroll_scheduled_hours_this_month_label') }}</th>
                                <td class="p-2">{{ $scheduledHoursThisMonth }} {{ __('messages.hour_word') }}</td>
                            </tr>
                        @endif

                        <tr class="border-b bg-blue-50">
                            <th class="p-2">{{ __('messages.payroll_due_salary_total_label') }}</th>
                            <td class="p-2 font-bold">{{ number_format($grossSalary, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6 mb-4">
                <h3 class="font-bold mb-4">{{ __('messages.advances') }}</h3>

                @forelse ($advances as $advance)
                    <div class="border rounded-lg p-4 mb-3">
                        <div class="flex justify-between text-sm mb-2">
                            <span>{{ __('messages.staff_advances_amount_label') }}: <strong>{{ number_format($advance->amount, 2) }}</strong></span>
                            <span>{{ __('messages.staff_advances_date_given_label') }}: {{ $advance->date_given->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-3">
                            <span>{{ __('messages.staff_advances_col_total_deducted') }}: {{ number_format($advance->total_deducted, 2) }}</span>
                            <span class="font-bold {{ $advance->remaining_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ __('messages.remaining_amount') }}: {{ number_format($advance->remaining_balance, 2) }}
                            </span>
                        </div>

                        @if ($advance->remaining_balance > 0)
                            <form method="POST" action="{{ route('admin.payroll.deductions.store', $advance) }}" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="year" value="{{ $year }}">
                                <input type="hidden" name="month" value="{{ $month }}">
                                <input type="number" step="0.01" name="amount" placeholder="{{ __('messages.payroll_deduction_amount_placeholder') }}"
                                       class="border rounded p-2 flex-1" required max="{{ $advance->remaining_balance }}">
                                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700 text-sm">
                                    {{ __('messages.payroll_record_deduction_button') }}
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">{{ __('messages.payroll_no_advances_for_person_empty') }}</p>
                @endforelse
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="font-bold mb-4">{{ __('messages.payroll_final_summary_title') }}</h3>
                <table class="w-full text-right border-collapse text-sm">
                    <tbody>
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50 w-1/2">{{ __('messages.payroll_col_due_salary') }}</th>
                            <td class="p-2">{{ number_format($grossSalary, 2) }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50">{{ __('messages.payroll_deducted_this_month_label') }}</th>
                            <td class="p-2 text-red-600">- {{ number_format($thisMonthDeduction, 2) }}</td>
                        </tr>
                        <tr class="bg-green-50">
                            <th class="p-2 text-lg">{{ __('messages.payroll_net_payable_label') }}</th>
                            <td class="p-2 text-lg font-bold text-green-700">{{ number_format($netPayable, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>