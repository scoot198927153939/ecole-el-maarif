<style>
    body { font-family: dejavusans; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; padding: 20px; }
    h1 { text-align: center; font-size: 20px; margin-bottom: 4px; }
    h2 { text-align: center; font-size: 13px; color: #555; font-weight: normal; margin-top: 0; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    td, th { border: 1px solid #333; padding: 8px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
    th { background-color: #f0f0f0; width: 35%; }
    .section-title { font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 8px; background-color: #e5e7eb; padding: 6px; }
</style>

<h1>{{ __('messages.pdf_student_card_title') }}</h1>
<h2>{{ __('messages.pdf_school_system_subtitle') }}</h2>

<div class="section-title">{{ __('messages.pdf_student_info_section') }}</div>
<table>
    <tr>
        <th>{{ __('messages.students_number_label') }}</th>
        <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->student_number }}</span></td>
    </tr>
    @if ($student->school_number)
        <tr>
            <th>{{ __('messages.pdf_school_number_label') }}</th>
            <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->school_number }}</span></td>
        </tr>
    @endif
    @if ($student->national_id)
        <tr>
            <th>{{ __('messages.pdf_national_id_label') }}</th>
            <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->national_id }}</span></td>
        </tr>
    @endif
    <tr>
        <th>{{ __('messages.students_col_full_name') }}</th>
        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.students_birth_date_label') }}</th>
        <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->birth_date->format('Y-m-d') }}</span></td>
    </tr>
    @if ($student->birth_place)
        <tr>
            <th>{{ __('messages.pdf_birth_place_label') }}</th>
            <td>{{ $student->birth_place }}</td>
        </tr>
    @endif
    <tr>
        <th>{{ __('messages.class') }}</th>
        <td>
            @if ($currentClass)
                {{ $currentClass->name }} <span dir="ltr" style="unicode-bidi: embed;">({{ $currentClass->academicYear->name ?? '' }})</span>
            @else
                —
            @endif
        </td>
    </tr>
    <tr>
        <th>{{ __('messages.status') }}</th>
        <td>
            @if ($student->status === 'active') {{ __('messages.students_status_active') }}
            @elseif ($student->status === 'graduated') {{ __('messages.students_status_graduated') }}
            @else {{ __('messages.students_status_withdrawn') }}
            @endif
        </td>
    </tr>
</table>

<div class="section-title">{{ __('messages.pdf_guardian_info_section') }}</div>
@if ($student->guardian)
    <table>
        <tr>
            <th>{{ __('messages.name') }}</th>
            <td>{{ $student->guardian->name }}</td>
        </tr>
        @if ($student->guardian->profession)
            <tr>
                <th>{{ __('messages.profession') }}</th>
                <td>{{ $student->guardian->profession }}</td>
            </tr>
        @endif
        <tr>
            <th>{{ __('messages.students_col_phone1') }}</th>
            <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->guardian->phone1 }}</span></td>
        </tr>
        <tr>
            <th>{{ __('messages.students_col_whatsapp') }}</th>
            <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->guardian->whatsapp }}</span></td>
        </tr>
        @if ($student->guardian->phone2)
            <tr>
                <th>{{ __('messages.pdf_phone2_label') }}</th>
                <td><span dir="ltr" style="unicode-bidi: embed;">{{ $student->guardian->phone2 }}</span></td>
            </tr>
        @endif
        @if ($student->guardian->address)
            <tr>
                <th>{{ __('messages.address') }}</th>
                <td>{{ $student->guardian->address }}</td>
            </tr>
        @endif
    </table>
@else
    <p>{{ __('messages.pdf_no_guardian_linked') }}</p>
@endif