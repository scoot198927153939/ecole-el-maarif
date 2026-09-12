<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.staff_advances_page_title') }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.staff_advances_list_title') }}</h3>
                    <a href="{{ route('admin.staff-advances.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('messages.staff_advances_add_button') }}</a>
                </div>

                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.type') }}</th>
                            <th class="p-2">{{ __('messages.staff_advances_amount_label') }}</th>
                            <th class="p-2">{{ __('messages.staff_advances_date_given_label') }}</th>
                            <th class="p-2">{{ __('messages.staff_advances_col_total_deducted') }}</th>
                            <th class="p-2">{{ __('messages.remaining_amount') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($advances as $advance)
                            <tr class="border-b">
                                <td class="p-2 font-medium">{{ $advance->staff_name }}</td>
                                <td class="p-2">{{ $advance->staff_type === 'teacher' ? __('messages.users_role_teacher') : __('messages.staff_advances_staff_type_staff_member') }}</td>
                                <td class="p-2">{{ number_format($advance->amount, 2) }}</td>
                                <td class="p-2">{{ $advance->date_given->format('Y-m-d') }}</td>
                                <td class="p-2">{{ number_format($advance->total_deducted, 2) }}</td>
                                <td class="p-2 font-bold {{ $advance->remaining_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($advance->remaining_balance, 2) }}
                                </td>
                                <td class="p-2">
                                    <form action="{{ route('admin.staff-advances.destroy', $advance) }}" method="POST"
                                          onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-gray-500">{{ __('messages.staff_advances_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>