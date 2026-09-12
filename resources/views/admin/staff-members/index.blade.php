<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.card_staff_members_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.staff_members_list_title') }}</h3>
                    <a href="{{ route('admin.staff-members.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.staff_members_add_button') }}
                    </a>
                </div>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.role') }}</th>
                            <th class="p-2">{{ __('messages.phone') }}</th>
                            <th class="p-2">{{ __('messages.staff_members_col_salary_type') }}</th>
                            <th class="p-2">{{ __('messages.status') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($staffMembers as $staff)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $staff->first_name }} {{ $staff->last_name }}</td>
                                <td class="p-2">{{ $staff->roleLabel() }}</td>
                                <td class="p-2"><span dir="ltr" style="unicode-bidi: embed;">{{ $staff->phone ?? '-' }}</span></td>
                                <td class="p-2">{{ $staff->salary_type === 'fixed' ? __('messages.staff_members_salary_fixed_short') : __('messages.staff_members_salary_hourly_short') }}</td>
                                <td class="p-2">{{ $staff->status === 'active' ? __('messages.students_status_active') : __('messages.staff_members_status_inactive') }}</td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.staff-members.edit', $staff) }}" class="text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                                    <form action="{{ route('admin.staff-members.destroy', $staff) }}" method="POST" class="inline"
                                          onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">{{ __('messages.staff_members_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>