<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.partners_show_title_prefix') }} {{ $partner->name }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6 mb-4">
                <table class="w-full text-right border-collapse text-sm">
                    <tbody>
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50 w-1/2">{{ __('messages.partners_percentage_row_label') }}</th>
                            <td class="p-2">{{ $partner->percentage }}%</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50">{{ __('messages.partners_net_profit_row_label') }}</th>
                            <td class="p-2">{{ number_format($netProfit, 2) }}</td>
                        </tr>
                        <tr class="border-b bg-blue-50">
                            <th class="p-2">{{ __('messages.partners_col_entitled') }}</th>
                            <td class="p-2 font-bold">{{ number_format($entitled, 2) }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50">{{ __('messages.partners_total_withdrawn_row_label') }}</th>
                            <td class="p-2">{{ number_format($partner->total_withdrawn, 2) }}</td>
                        </tr>
                        <tr class="{{ $entitled - $partner->total_withdrawn < 0 ? 'bg-red-50' : 'bg-green-50' }}">
                            <th class="p-2">{{ __('messages.difference') }}</th>
                            <td class="p-2 font-bold {{ $entitled - $partner->total_withdrawn < 0 ? 'text-red-600' : 'text-green-600' }}">
                                @if ($entitled - $partner->total_withdrawn < 0)
                                    {{ __('messages.partners_over_withdrawn_row_prefix') }} {{ number_format(abs($entitled - $partner->total_withdrawn), 2) }}
                                @else
                                    {{ number_format($entitled - $partner->total_withdrawn, 2) }}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6 mb-4">
                <h3 class="font-bold mb-4">{{ __('messages.partners_new_withdrawal_title') }}</h3>
                <form method="POST" action="{{ route('admin.partners.withdrawals.store', $partner) }}" class="flex gap-2 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">{{ __('messages.amount') }}</label>
                        <input type="number" step="0.01" name="amount" class="w-full border rounded-lg p-2" required>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">{{ __('messages.date') }}</label>
                        <input type="date" name="withdrawal_date" value="{{ now()->format('Y-m-d') }}" class="w-full border rounded-lg p-2" required>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">{{ __('messages.staff_advances_note_optional_label') }}</label>
                        <input type="text" name="note" class="w-full border rounded-lg p-2">
                    </div>
                    <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700">{{ __('messages.partners_record_button') }}</button>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="font-bold mb-4">{{ __('messages.partners_withdrawals_log_title') }}</h3>
                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.date') }}</th>
                            <th class="p-2">{{ __('messages.amount') }}</th>
                            <th class="p-2">{{ __('messages.partners_col_note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($withdrawals as $withdrawal)
                            <tr class="border-b">
                                <td class="p-2">{{ $withdrawal->withdrawal_date->format('Y-m-d') }}</td>
                                <td class="p-2 font-bold">{{ number_format($withdrawal->amount, 2) }}</td>
                                <td class="p-2 text-gray-600">{{ $withdrawal->note ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-4 text-center text-gray-500">{{ __('messages.partners_no_withdrawals_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>