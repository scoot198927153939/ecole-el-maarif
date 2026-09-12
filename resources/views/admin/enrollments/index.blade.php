<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.card_enrollments_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.enrollments_list_title') }}</h3>
                    <a href="{{ route('admin.enrollments.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ __('messages.enrollments_add_button') }}
                    </a>
                </div>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.student') }}</th>
                            <th class="p-2">{{ __('messages.class') }}</th>
                            <th class="p-2">{{ __('messages.academic_year') }}</th>
                            <th class="p-2">{{ __('messages.enrollments_col_enrollment_date') }}</th>
                            <th class="p-2">{{ __('messages.status') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
                                <td class="p-2">{{ $enrollment->classRoom->name }}</td>
                                <td class="p-2">{{ $enrollment->academicYear->name }}</td>
                                <td class="p-2">{{ $enrollment->enrollment_date->format('Y-m-d') }}</td>
                                <td class="p-2">
                                    @if ($enrollment->enrollment_status === 'active')
                                        <span class="text-green-600">{{ __('messages.students_status_active') }}</span>
                                    @elseif ($enrollment->enrollment_status === 'transferred')
                                        <span class="text-yellow-600">{{ __('messages.enrollments_status_transferred') }}</span>
                                    @else
                                        <span class="text-blue-600">{{ __('messages.enrollments_status_completed') }}</span>
                                    @endif
                                </td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('students.profile', $enrollment) }}"
                                       class="text-indigo-600 hover:underline font-bold">{{ __('messages.enrollments_full_profile_link') }}</a>
                                    <a href="{{ route('reports.term1.pdf', $enrollment) }}" target="_blank"
                                       class="text-purple-600 hover:underline">{{ __('messages.classes_report_term1') }}</a>
                                    <a href="{{ route('reports.term2.pdf', $enrollment) }}" target="_blank"
                                       class="text-purple-600 hover:underline">{{ __('messages.classes_report_term2') }}</a>
                                    <a href="{{ route('reports.term3.pdf', $enrollment) }}" target="_blank"
                                       class="text-purple-600 hover:underline">{{ __('messages.classes_report_term3') }}</a>
                                    <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                                    <form action="{{ route('admin.enrollments.destroy', $enrollment) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">
                                    {{ __('messages.enrollments_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>