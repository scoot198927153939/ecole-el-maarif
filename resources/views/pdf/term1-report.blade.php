<style>
    body { font-family: dejavusans; padding: 10px; }
    h1 { text-align: center; font-size: 20px; margin-bottom: 5px; }
    p.subinfo { text-align: center; font-size: 13px; margin-bottom: 15px; }
    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px solid #333; padding: 6px; text-align: center; font-size: 12px; }
    th { background-color: #f0f0f0; }
    .subject-col { text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
    .final { margin-top: 20px; text-align: center; }
    .final .avg { font-size: 26px; font-weight: bold; }
</style>

<h1>{{ __('messages.pdf_term1_sheet_title') }}</h1>
<p class="subinfo">
    {{ __('messages.student') }}: {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
    | {{ __('messages.class') }}: <span dir="ltr" style="unicode-bidi: embed;">{{ preg_replace('/\d+$/', '', $enrollment->classRoom->name) }}</span>
    | {{ __('messages.academic_year') }}: <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->academicYear->name }}</span>
</p>

<table>
    <thead>
        <tr>
            <th>{{ __('messages.subject') }}</th>
            <th>{{ __('messages.coefficient') }}</th>
            <th>{{ __('messages.pdf_test_col_prefix') }} 1</th>
            <th>{{ __('messages.pdf_test_col_prefix') }} 2</th>
            <th>{{ __('messages.pdf_test_col_prefix') }} 3</th>
            <th>{{ __('messages.pdf_term1_exam_col') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td class="subject-col">{{ $row['subject']->name }}</td>
                <td>{{ $row['subject']->coefficient }}</td>
                @for ($i = 0; $i < 3; $i++)
                    <td>{{ $row['tests'][$i]->score ?? '-' }}</td>
                @endfor
                <td>{{ $row['comp1'] ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="final">
    <table style="width: 60%; margin: 10px auto;">
        <tr>
            <th>{{ __('messages.pdf_tests_average_label') }}</th>
            <th>{{ __('messages.pdf_term1_exam_average_label') }}</th>
            <th>{{ __('messages.pdf_mention_col') }}</th>
            <th>{{ __('messages.pdf_decision_col') }}</th>
        </tr>
        <tr>
            <td>{{ $overallTestsAvg ?? '-' }}</td>
            <td>{{ $overallComp1Avg ?? '-' }}</td>
            <td>{{ $mention }}</td>
            <td>{{ $decision }}</td>
        </tr>
    </table>
    <p class="avg">{{ __('messages.pdf_overall_average_label') }} {{ $overallAverage ?? '-' }}</p>
</div>