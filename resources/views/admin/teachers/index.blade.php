<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.card_teachers_title') }}
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
                <h3 class="text-lg font-bold mb-4">{{ __('messages.teachers_list_title') }}</h3>

                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.specialization') }}</th>
                            <th class="p-2">{{ __('messages.staff_members_col_salary_type') }}</th>
                            <th class="p-2">{{ __('messages.teachers_col_is_partner') }}</th>
                            <th class="p-2">{{ __('messages.status') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $teacher)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2 font-medium">{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                <td class="p-2">{{ $teacher->specialization ?: '—' }}</td>
                                <td class="p-2">{{ $teacher->salary_type === 'fixed' ? __('messages.staff_members_salary_fixed_short') : __('messages.staff_members_salary_hourly_short') }}</td>
                                <td class="p-2">{{ $teacher->is_partner ? __('messages.yes') : __('messages.no') }}</td>
                                <td class="p-2">
                                    @if ($teacher->status === 'active')
                                        <span class="text-green-600">{{ __('messages.students_status_active') }}</span>
                                    @else
                                        <span class="text-red-600">{{ __('messages.staff_members_status_inactive') }}</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">{{ __('messages.teachers_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>