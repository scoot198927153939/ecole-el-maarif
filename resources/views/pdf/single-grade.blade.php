<style>
    body { font-family: dejavusans; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; padding: 10px; }
    h1 { text-align: center; font-size: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    td, th { border: 1px solid #333; padding: 10px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
    th { background-color: #f0f0f0; width: 35%; }
    .score { font-size: 24px; font-weight: bold; text-align: center; }
</style>

<h1>{{ __('messages.pdf_grade_card_title') }}</h1>

<table>
    <tr>
        <th>{{ __('messages.student') }}</th>
        <td>{{ $grade->enrollment->student->first_name }} {{ $grade->enrollment->student->last_name }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.students_number_label') }}</th>
        <td>{{ $grade->enrollment->student->student_number }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.pdf_section_label') }}</th>
        <td>{{ $grade->assessment->classRoom->name }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.academic_year') }}</th>
        <td>{{ $grade->enrollment->academicYear->name }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.subject') }}</th>
        <td>{{ $grade->assessment->subject->name }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.assessment') }}</th>
        <td>{{ $grade->assessment->title }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.pdf_term_label') }}</th>
        <td>
            @if ($grade->assessment->term == 1) {{ __('messages.pdf_term_1') }}
            @elseif ($grade->assessment->term == 2) {{ __('messages.pdf_term_2') }}
            @else {{ __('messages.pdf_term_3') }}
            @endif
        </td>
    </tr>
    <tr>
        <th>{{ __('messages.pdf_grade_label') }}</th>
        <td class="score">
            @if ($grade->is_absent)
                {{ __('messages.staff_attendance_status_absent') }}
            @else
                {{ $grade->score }} / 20
            @endif
        </td>
    </tr>
    @if ($grade->teacher_note)
    <tr>
        <th>{{ __('messages.pdf_teacher_note_label') }}</th>
        <td>{{ $grade->teacher_note }}</td>
    </tr>
    @endif
</table>