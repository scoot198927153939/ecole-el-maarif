<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.class_rosters_show_title_prefix') }} {{ $class->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('admin.students.rosters.index') }}" class="text-blue-600 hover:underline text-sm inline-block">
                    {{ __('messages.class_rosters_back_link') }}
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('admin.students.rosters.csv', $class) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                        {{ __('messages.class_rosters_export_csv_button') }}
                    </a>
                    <a href="{{ route('admin.students.rosters.pdf', $class) }}" target="_blank"
                       class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 text-sm">
                        {{ __('messages.assessments_export_pdf_link') }}
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-4 overflow-x-auto">
                <table class="w-full text-right border-collapse text-sm whitespace-nowrap">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.students_col_number') }}</th>
                            <th class="p-2">{{ __('messages.students_col_full_name') }}</th>
                            <th class="p-2">{{ __('messages.students_birth_date_label') }}</th>
                            <th class="p-2">{{ __('messages.pdf_birth_place_label') }}</th>
                            <th class="p-2">{{ __('messages.pdf_national_id_label') }}</th>
                            <th class="p-2">{{ __('messages.pdf_school_number_label') }}</th>
                            <th class="p-2">{{ __('messages.status') }}</th>
                            <th class="p-2">{{ __('messages.class_rosters_col_guardian_name') }}</th>
                            <th class="p-2">{{ __('messages.profession') }}</th>
                            <th class="p-2">{{ __('messages.students_col_phone1') }}</th>
                            <th class="p-2">{{ __('messages.students_col_whatsapp') }}</th>
                            <th class="p-2">{{ __('messages.pdf_phone2_label') }}</th>
                            <th class="p-2">{{ __('messages.address') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            @php $student = $enrollment->student; $guardian = $student->guardian; @endphp
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $student->student_number }}</td>
                                <td class="p-2 font-medium">{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td class="p-2">{{ optional($student->birth_date)->format('Y-m-d') }}</td>
                                <td class="p-2">{{ $student->birth_place ?? '-' }}</td>
                                <td class="p-2">{{ $student->national_id ?? '-' }}</td>
                                <td class="p-2">{{ $student->school_number ?? '-' }}</td>
                                <td class="p-2">
                                    @if ($student->status === 'active')
                                        <span class="text-green-600">{{ __('messages.students_status_active') }}</span>
                                    @elseif ($student->status === 'graduated')
                                        <span class="text-blue-600">{{ __('messages.students_status_graduated') }}</span>
                                    @else
                                        <span class="text-red-600">{{ __('messages.students_status_withdrawn') }}</span>
                                    @endif
                                </td>
                                <td class="p-2">{{ $guardian->name ?? '-' }}</td>
                                <td class="p-2">{{ $guardian->profession ?? '-' }}</td>
                                <td class="p-2">{{ $guardian->phone1 ?? '-' }}</td>
                                <td class="p-2">{{ $guardian->whatsapp ?? '-' }}</td>
                                <td class="p-2">{{ $guardian->phone2 ?? '-' }}</td>
                                <td class="p-2">{{ $guardian->address ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="p-4 text-center text-gray-500">
                                    {{ __('messages.class_rosters_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>