<style>
    body { font-family: dejavusans; padding: 10px; }
    h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
    p.subinfo { text-align: center; font-size: 12px; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px solid #333; padding: 5px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; font-size: 10px; }
    th { background-color: #f0f0f0; }
    .center { text-align: center; }
</style>

<h1>{{ __('messages.pdf_class_roster_title') }} — {{ $class->name }}</h1>
<p class="subinfo">
    {{ __('messages.academic_year') }}: <span dir="ltr" style="unicode-bidi: embed;">{{ $class->academicYear->name ?? '' }}</span>
    — {{ __('messages.class_rosters_col_student_count') }}: {{ $enrollments->count() }}
</p>

<table>
    <thead>
        <tr>
            <th>{{ __('messages.students_col_number') }}</th>
            <th>{{ __('messages.students_col_full_name') }}</th>
            <th>{{ __('messages.students_birth_date_label') }}</th>
            <th>{{ __('messages.pdf_birth_place_label') }}</th>
            <th>{{ __('messages.pdf_national_id_label') }}</th>
            <th>{{ __('messages.pdf_school_number_label') }}</th>
            <th class="center">{{ __('messages.status') }}</th>
            <th>{{ __('messages.class_rosters_col_guardian_name') }}</th>
            <th>{{ __('messages.profession') }}</th>
            <th>{{ __('messages.students_col_phone1') }}</th>
            <th>{{ __('messages.students_col_whatsapp') }}</th>
            <th>{{ __('messages.pdf_phone2_label') }}</th>
            <th>{{ __('messages.address') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($enrollments as $enrollment)
            @php $student = $enrollment->student; $guardian = $student->guardian; @endphp
            <tr>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->student_number }}</span></td>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ optional($student->birth_date)->format('Y-m-d') }}</span></td>
                <td>{{ $student->birth_place }}</td>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->national_id }}</span></td>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->school_number }}</span></td>
                <td class="center">
                    @if ($student->status === 'active') {{ __('messages.students_status_active') }}
                    @elseif ($student->status === 'graduated') {{ __('messages.students_status_graduated') }}
                    @else {{ __('messages.students_status_withdrawn') }}
                    @endif
                </td>
                <td>{{ $guardian->name ?? '-' }}</td>
                <td>{{ $guardian->profession ?? '-' }}</td>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ $guardian->phone1 ?? '-' }}</span></td>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ $guardian->whatsapp ?? '-' }}</span></td>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ $guardian->phone2 ?? '-' }}</span></td>
                <td>{{ $guardian->address ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="13" class="center">{{ __('messages.class_rosters_empty') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>