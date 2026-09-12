<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.students_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    @if (session('new_student_id'))
                        <a href="{{ route('admin.students.card.pdf', session('new_student_id')) }}" target="_blank"
                           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm whitespace-nowrap">
                            {{ __('messages.students_success_card_link') }}
                        </a>
                    @endif
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.students_list_title') }}</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.students.rosters.index') }}"
                           class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.class_rosters_list_link') }}
                        </a>
                        <a href="{{ route('admin.students.create') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.students_add_button') }}
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.students_col_class') }}</th>
                                <th class="p-2">{{ __('messages.students_col_number') }}</th>
                                <th class="p-2">{{ __('messages.students_col_full_name') }}</th>
                                <th class="p-2">{{ __('messages.students_col_birth_date') }}</th>
                                <th class="p-2">{{ __('messages.students_col_status') }}</th>
                                <th class="p-2">{{ __('messages.students_col_guardian') }}</th>
                                <th class="p-2">{{ __('messages.students_col_profession') }}</th>
                                <th class="p-2">{{ __('messages.students_col_phone1') }}</th>
                                <th class="p-2">{{ __('messages.students_col_whatsapp') }}</th>
                                <th class="p-2">{{ __('messages.students_col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2 font-medium">{{ $student->current_class->name ?? '—' }}</td>
                                    <td class="p-2">{{ $student->student_number }}</td>
                                    <td class="p-2">{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td class="p-2">{{ $student->birth_date->format('Y-m-d') }}</td>
                                    <td class="p-2">
                                        @if ($student->status === 'active')
                                            <span class="text-green-600">{{ __('messages.students_status_active') }}</span>
                                        @elseif ($student->status === 'graduated')
                                            <span class="text-blue-600">{{ __('messages.students_status_graduated') }}</span>
                                        @else
                                            <span class="text-red-600">{{ __('messages.students_status_withdrawn') }}</span>
                                        @endif
                                    </td>
                                    <td class="p-2">{{ $student->guardian->name ?? '—' }}</td>
                                    <td class="p-2">{{ $student->guardian->profession ?? '—' }}</td>
                                    <td class="p-2">{{ $student->guardian->phone1 ?? '—' }}</td>
                                    <td class="p-2">{{ $student->guardian->whatsapp ?? '—' }}</td>
                                    <td class="p-2 space-x-2 space-x-reverse whitespace-nowrap">
                                        <a href="{{ route('admin.students.card.pdf', $student) }}" target="_blank"
                                           class="text-green-600 hover:underline">{{ __('messages.students_action_card') }}</a>
                                        <a href="{{ route('admin.students.edit', $student) }}"
                                           class="text-blue-600 hover:underline">{{ __('messages.students_action_edit') }}</a>
                                        <form action="{{ route('admin.students.destroy', $student) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('{{ __('messages.students_delete_confirm') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">{{ __('messages.students_action_delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="p-4 text-center text-gray-500">
                                        {{ __('messages.students_empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>