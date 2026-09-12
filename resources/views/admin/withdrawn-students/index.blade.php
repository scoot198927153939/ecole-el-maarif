<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.card_withdrawn_students_title') }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">
                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.student') }}</th>
                            <th class="p-2">{{ __('messages.students_number_label') }}</th>
                            <th class="p-2">{{ __('messages.withdrawn_students_col_last_class') }}</th>
                            <th class="p-2">{{ __('messages.guardian') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            @php $lastEnrollment = $student->enrollments->first(); @endphp
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2 font-medium">{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td class="p-2">{{ $student->student_number }}</td>
                                <td class="p-2">{{ $lastEnrollment?->classRoom->name ?? '—' }}</td>
                                <td class="p-2">{{ $student->guardian->name ?? '—' }}</td>
                                <td class="p-2">
                                    @if ($lastEnrollment)
                                        <a href="{{ route('admin.withdrawn-students.show', $lastEnrollment) }}"
                                           class="text-blue-600 hover:underline">{{ __('messages.withdrawn_students_details_refund_link') }}</a>
                                    @else
                                        <span class="text-gray-400">{{ __('messages.withdrawn_students_no_enrollment') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">{{ __('messages.withdrawn_students_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>