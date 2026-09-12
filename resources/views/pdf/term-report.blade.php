<style>
    body { font-family: dejavusans; padding: 10px; }
    h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px solid #333; padding: 8px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; font-size: 13px; }
    th { background-color: #f0f0f0; }
    .center { text-align: center; }
    .info-table td { border: none; text-align: center; }
    .final { margin-top: 20px; text-align: center; }
    .final .avg { font-size: 26px; font-weight: bold; }
</style>

<h1>
    {{ __('messages.pdf_results_sheet_title') }} —
    @if ($term == 1) {{ __('messages.pdf_term_full_1') }}
    @elseif ($term == 2) {{ __('messages.pdf_term_full_2') }}
    @else {{ __('messages.pdf_term_full_3') }}
    @endif
</h1>

<table class="info-table" style="margin-bottom: 15px;">
    <tr>
        <td style="width: 50%;">{{ __('messages.student') }}: {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
        <td style="width: 50%;">{{ __('messages.students_number_label') }}: <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->student->student_number }}</span></td>
    </tr>
    <tr>
        <td style="width: 50%;">{{ __('messages.class') }}: <span dir="ltr" style="unicode-bidi: embed;">{{ preg_replace('/\d+$/', '', $enrollment->classRoom->name) }}</span></td>
        <td style="width: 50%;">{{ __('messages.academic_year') }}: <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->academicYear->name }}</span></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>{{ __('messages.subject') }}</th>
            <th class="center">{{ __('messages.pdf_col_subject_coefficient') }}</th>
            <th class="center">{{ __('messages.pdf_col_grades_count') }}</th>
            <th class="center">{{ __('messages.pdf_col_subject_average') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($subjectsData as $data)
            <tr>
                <td>{{ $data['subject']->name }}</td>
                <td class="center">{{ $data['subject']->coefficient }}</td>
                <td class="center">{{ $data['grades']->count() }}</td>
                <td class="center">{{ $data['average'] !== null ? $data['average'] : '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="center">{{ __('messages.pdf_no_grades_this_term_empty') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="final">
    <p>{{ __('messages.pdf_term_overall_average_label') }}</p>
    <p class="avg">{{ $termAverage !== null ? $termAverage : '-' }}</p>
    <p>{{ __('messages.pdf_mention_label') }} {{ $mention }}</p>
</div>