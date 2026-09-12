<x-guardian-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ $student->first_name }} {{ $student->last_name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('parent.dashboard') }}" class="text-blue-600 hover:underline text-sm inline-block">
                {{ __('messages.parent_student_back_link') }}
            </a>

            @if (! $enrollment)
                <div class="bg-white shadow-sm rounded-xl p-6 text-center text-gray-500">
                    {{ __('messages.parent_no_enrollment_note') }}
                </div>
            @else
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <p><strong>{{ __('messages.students_number_label') }}:</strong> {{ $student->student_number }}</p>
                    <p><strong>{{ __('messages.pdf_section_label') }}:</strong> {{ $enrollment->classRoom->name }}</p>
                    <p><strong>{{ __('messages.academic_year') }}:</strong> {{ $enrollment->academicYear->name }}</p>
                </div>

                {{-- النتائج الدراسية --}}
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.parent_section_results_title') }}</h3>

                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.subject') }}</th>
                                <th class="p-2">{{ __('messages.coefficient') }}</th>
                                <th class="p-2">{{ __('messages.reports_col_annual_average') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subjectsData as $data)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $data['subject']->name }}</td>
                                    <td class="p-2">{{ $data['subject']->coefficient }}</td>
                                    <td class="p-2 font-bold">{{ $data['average'] !== null ? $data['average'] : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-gray-500">{{ __('messages.reports_no_grades_empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-6 text-center border-t pt-6">
                        <p class="text-gray-500 text-sm">{{ __('messages.reports_overall_annual_average_title') }}</p>
                        <p class="text-3xl font-bold text-blue-600 mb-2">{{ $annualAverage !== null ? $annualAverage : '-' }}</p>
                        <div class="flex justify-center gap-10">
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('messages.pdf_decision_col') }}</p>
                                <p class="text-lg font-bold
                                    @if ($decision === __('messages.pdf_mention_passed')) text-green-600
                                    @elseif ($decision === __('messages.pdf_mention_failed')) text-red-600
                                    @endif
                                ">{{ $decision }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('messages.pdf_mention_col') }}</p>
                                <p class="text-lg font-bold text-blue-600">{{ $annualMention }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الوضعية المالية --}}
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.parent_section_finance_title') }}</h3>

                    <div class="grid grid-cols-3 gap-4 text-center mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 text-sm">{{ __('messages.required_amount') }}</p>
                            <p class="text-xl font-bold text-gray-800">{{ number_format($requiredAmount, 2) }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-gray-500 text-sm">{{ __('messages.paid_amount') }}</p>
                            <p class="text-xl font-bold text-green-700">{{ number_format($paidAmount, 2) }}</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <p class="text-gray-500 text-sm">{{ __('messages.remaining_amount') }}</p>
                            <p class="text-xl font-bold text-red-700">{{ number_format($remainingAmount, 2) }}</p>
                        </div>
                    </div>

                    <h4 class="font-bold mb-2">{{ __('messages.parent_payments_log_title') }}</h4>
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.fee_payments_payment_date_label') }}</th>
                                <th class="p-2">{{ __('messages.amount') }}</th>
                                <th class="p-2">{{ __('messages.method') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $payment->payment_date->format('Y-m-d') }}</td>
                                    <td class="p-2">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="p-2">
                                        {{ $payment->method === 'cash' ? __('messages.fee_payments_method_cash') : __('messages.fee_payments_method_transfer_short') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-gray-500">{{ __('messages.fee_payments_no_payments_empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- سجل الدروس --}}
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.parent_section_lessons_title') }}</h3>

                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.pdf_lesson_date_label') }}</th>
                                <th class="p-2">{{ __('messages.subject') }}</th>
                                <th class="p-2">{{ __('messages.pdf_lesson_title_label') }}</th>
                                <th class="p-2">{{ __('messages.teacher') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lessonLogs as $log)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $log->lesson_date->format('Y-m-d') }}</td>
                                    <td class="p-2">{{ $log->assignment->subject->name ?? '-' }}</td>
                                    <td class="p-2">{{ $log->title }}</td>
                                    <td class="p-2">{{ $log->assignment->teacher->first_name ?? '' }} {{ $log->assignment->teacher->last_name ?? '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">{{ __('messages.parent_lesson_log_empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- الجدول الزمني --}}
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4">{{ __('messages.parent_section_schedule_title') }}</h3>

                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.day') }}</th>
                                <th class="p-2">{{ __('messages.session_number') }}</th>
                                <th class="p-2">{{ __('messages.subject') }}</th>
                                <th class="p-2">{{ __('messages.teacher') }}</th>
                                <th class="p-2">{{ __('messages.start_time') }}</th>
                                <th class="p-2">{{ __('messages.end_time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dayNames = [
                                    1 => __('messages.day_monday'),
                                    2 => __('messages.day_tuesday'),
                                    3 => __('messages.day_wednesday'),
                                    4 => __('messages.day_thursday'),
                                    5 => __('messages.day_friday'),
                                    6 => __('messages.day_saturday'),
                                ];
                            @endphp
                            @forelse ($schedule as $item)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $dayNames[$item->day_of_week] ?? '-' }}</td>
                                    <td class="p-2">{{ $item->session_number }}</td>
                                    <td class="p-2">{{ $item->assignment->subject->name ?? '-' }}</td>
                                    <td class="p-2">{{ $item->assignment->teacher->first_name ?? '' }} {{ $item->assignment->teacher->last_name ?? '' }}</td>
                                    <td class="p-2"><span dir="ltr">{{ $item->start_time }}</span></td>
                                    <td class="p-2"><span dir="ltr">{{ $item->end_time }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500">{{ __('messages.parent_schedule_empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-guardian-layout>
