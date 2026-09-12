<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.reports_student_results_title_prefix') }} {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <p><strong>{{ __('messages.students_number_label') }}:</strong> {{ $enrollment->student->student_number }}</p>
                <p><strong>{{ __('messages.pdf_section_label') }}:</strong> {{ $enrollment->classRoom->name }}</p>
                <p><strong>{{ __('messages.academic_year') }}:</strong> {{ $enrollment->academicYear->name }}</p>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.reports_subject_averages_title') }}</h3>

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
                                <td class="p-2 font-bold">
                                    {{ $data['average'] !== null ? $data['average'] : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-500">
                                    {{ __('messages.reports_no_grades_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                <h3 class="text-lg font-bold mb-2">{{ __('messages.reports_overall_annual_average_title') }}</h3>
                <p class="text-4xl font-bold text-blue-600 mb-4">
                    {{ $annualAverage !== null ? $annualAverage : '-' }}
                </p>

                <div class="flex justify-center gap-10">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.pdf_decision_col') }}</p>
                        <p class="text-xl font-bold
                            @if ($decision === __('messages.pdf_mention_passed')) text-green-600
                            @elseif ($decision === __('messages.pdf_mention_failed')) text-red-600
                            @endif
                        ">{{ $decision }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.pdf_mention_col') }}</p>
                        <p class="text-xl font-bold text-blue-600">{{ $annualMention }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>