<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.payroll_page_title') }}</h2>
    </x-slot>
    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('admin.payroll.index') }}" class="mb-4 flex gap-2">
                <select name="month" class="border rounded-lg p-2">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $m }}</option>
                    @endfor
                </select>
                <select name="year" class="border rounded-lg p-2">
                    @for ($y = now()->year - 1; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">{{ __('messages.view') }}</button>
            </form>

            <div class="bg-blue-600 text-white rounded-xl p-6 text-center mb-6">
                <p class="text-blue-100 mb-1">{{ __('messages.payroll_total_this_month_label') }}</p>
                <p class="text-3xl font-bold">{{ number_format($totalPayroll, 2) }} {{ __('messages.currency') }}</p>
            </div>

            @if ($partnerTeachers->count())
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-6">
                    <h3 class="font-bold mb-4">{{ __('messages.payroll_partner_choice_title') }}</h3>
                    <div class="space-y-3">
                        @foreach ($partnerTeachers as $pt)
                            @php $currentChoice = $choicesThisMonth->get($pt->id); @endphp
                            <div class="bg-white rounded-lg p-4 border border-amber-100">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium">{{ $pt->first_name }} {{ $pt->last_name }}</span>
                                    <span class="text-sm">
                                        {{ __('messages.payroll_current_choice_label') }}
                                        @if ($currentChoice)
                                            <strong>{{ $currentChoice->choice === 'salary' ? __('messages.payroll_choice_salary') : __('messages.payroll_choice_partner_share') }}</strong>
                                        @else
                                            <span class="text-gray-400">{{ __('messages.payroll_not_set_yet') }}</span>
                                        @endif
                                    </span>
                                </div>
                                <form method="POST" action="{{ route('admin.payroll.payment-choice.store') }}" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="teacher_id" value="{{ $pt->id }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <select name="choice" class="border rounded p-2 text-sm flex-1">
                                        <option value="salary" {{ $currentChoice?->choice === 'salary' ? 'selected' : '' }}>{{ __('messages.payroll_choice_salary') }}</option>
                                        <option value="partner_share" {{ $currentChoice?->choice === 'partner_share' ? 'selected' : '' }}>{{ __('messages.payroll_choice_partner_share') }}</option>
                                    </select>
                                    <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded text-sm hover:bg-amber-700">
                                        {{ __('messages.save') }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-3">
                        {{ __('messages.payroll_partner_note') }}
                    </p>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.role') }}</th>
                            <th class="p-2">{{ __('messages.staff_members_col_salary_type') }}</th>
                            <th class="p-2">{{ __('messages.staff_attendance_col_hours') }}</th>
                            <th class="p-2">{{ __('messages.payroll_col_due_salary') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allPayroll as $row)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $row['name'] }}</td>
                                <td class="p-2">{{ $row['type'] }}</td>
                                <td class="p-2">{{ $row['salary_type'] === 'fixed' ? __('messages.staff_members_salary_fixed_short') : __('messages.staff_members_salary_hourly_short') }}</td>
                                <td class="p-2">{{ $row['hours'] ?? '-' }}</td>
                                <td class="p-2 font-bold">{{ number_format($row['amount'], 2) }}</td>
                                <td class="p-2">
                                    <a href="{{ route('admin.payroll.show', ['type' => $row['type_key'], 'id' => $row['id'], 'year' => $year, 'month' => $month]) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.payroll_view_statement_link') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">{{ __('messages.payroll_no_active_staff_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>