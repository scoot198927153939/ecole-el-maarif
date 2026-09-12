<style>
    body { font-family: dejavusans; padding: 10px; }
    h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px solid #333; padding: 8px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; font-size: 13px; }
    th { background-color: #f0f0f0; }
    .center { text-align: center; }
    .info-table td { border: none; text-align: center; white-space: nowrap; }
</style>

<h1>{{ __('messages.pdf_results_sheet_title_prefix') }} {{ $assessment->title }}</h1>

<table class="info-table" style="margin-bottom: 15px;">
    <tr>
        <td style="width: 50%;">{{ __('messages.subject') }}: {{ $assessment->subject->name }}</td>
        <td style="width: 50%;">{{ __('messages.class') }}: <span dir="ltr" style="unicode-bidi: embed;">{{ preg_replace('/\d+$/', '', $assessment->classRoom->name) }}</span></td>
    </tr>
    <tr>
        <td style="width: 50%;">{{ __('messages.academic_year') }}: <span dir="ltr" style="unicode-bidi: embed;">{{ $assessment->academicYear->name }}</span></td>
        <td style="width: 50%;">
            {{ __('messages.pdf_term_label') }}
            @if ($assessment->term == 1) {{ __('messages.pdf_term_1') }}
            @elseif ($assessment->term == 2) {{ __('messages.pdf_term_2') }}
            @else {{ __('messages.pdf_term_3') }}
            @endif
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>{{ __('messages.students_number_label') }}</th>
            <th>{{ __('messages.pdf_student_name_col') }}</th>
            <th class="center">{{ __('messages.pdf_col_grade_out_of_20') }}</th>
            <th>{{ __('messages.pdf_teacher_note_label') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($enrollments as $enrollment)
            @php
                $grade = $grades->get($enrollment->id);
            @endphp
            <tr>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->student->student_number }}</span></td>
                <td>{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
                <td class="center">
                    @if (! $grade)
                        -
                    @elseif ($grade->is_absent)
                        {{ __('messages.staff_attendance_status_absent') }}
                    @else
                        {{ $grade->score }}
                    @endif
                </td>
                <td>{{ $grade->teacher_note ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="center">{{ __('messages.pdf_no_students_registered_empty') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>