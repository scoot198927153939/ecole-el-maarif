<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.students_profile_title_prefix') }} {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- البيانات الأساسية -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.students_profile_basic_info_title') }}</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <p><strong>{{ __('messages.name') }}:</strong> {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</p>
                    <p><strong>{{ __('messages.students_number_label') }}:</strong> <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->student->student_number }}</span></p>
                    <p><strong>{{ __('messages.class') }}:</strong> {{ $enrollment->classRoom->name }}</p>
                    <p><strong>{{ __('messages.academic_year') }}:</strong> <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->academicYear->name }}</span></p>
                    <p><strong>{{ __('messages.students_birth_date_label') }}:</strong> <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->student->birth_date?->format('Y-m-d') }}</span></p>
                    <p><strong>{{ __('messages.students_profile_guardian_name_label') }}</strong> {{ $enrollment->student->guardian_name }}</p>
                    <p><strong>{{ __('messages.students_profile_guardian_phone_label') }}</strong> <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->student->guardian_phone }}</span></p>
                    <p><strong>{{ __('messages.status') }}:</strong>
                        @if ($enrollment->student->status === 'active') {{ __('messages.students_status_active') }}
                        @elseif ($enrollment->student->status === 'graduated') {{ __('messages.students_status_graduated') }}
                        @else {{ __('messages.students_status_withdrawn') }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- المعدلات -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.students_profile_averages_title') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.composition_word') }} {{ __('messages.term_short_word') }} 1</p>
                        <p class="text-xl font-bold">{{ $term1Comp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.composition_word') }} {{ __('messages.term_short_word') }} 2</p>
                        <p class="text-xl font-bold">{{ $term2Comp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.composition_word') }} {{ __('messages.term_short_word') }} 3</p>
                        <p class="text-xl font-bold">{{ $term3Comp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.reports_overall_annual_average_title') }}</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $annualAverage ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex justify-center gap-10 mt-4">
                    <p><strong>{{ __('messages.pdf_mention_col') }}:</strong> {{ $mention }}</p>
                    <p><strong>{{ __('messages.pdf_decision_col') }}:</strong>
                        <span class="{{ $decision === __('messages.pdf_mention_passed') ? 'text-green-600' : ($decision === __('messages.pdf_mention_failed') ? 'text-red-600' : '') }}">
                            {{ $decision }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- الحضور -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.students_profile_attendance_title') }}</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.teacher_attendance_report_col_present') }}</p>
                        <p class="text-xl font-bold text-green-600">{{ $presentCount }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.teacher_attendance_report_col_absent') }}</p>
                        <p class="text-xl font-bold text-red-600">{{ $absentCount }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('messages.attendance_col_attendance_percent') }}</p>
                        <p class="text-xl font-bold">{{ $attendancePercent !== null ? $attendancePercent.'%' : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- روابط الكشوف -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.students_profile_export_title') }}</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('reports.term1.pdf', $enrollment) }}" target="_blank"
                       class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">{{ __('messages.students_profile_term1_report_link') }}</a>
                    <a href="{{ route('reports.term2.pdf', $enrollment) }}" target="_blank"
                       class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">{{ __('messages.students_profile_term2_report_link') }}</a>
                    <a href="{{ route('reports.term3.pdf', $enrollment) }}" target="_blank"
                       class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">{{ __('messages.students_profile_term3_report_link') }}</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>