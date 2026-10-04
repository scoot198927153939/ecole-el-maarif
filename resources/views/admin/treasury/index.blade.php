<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.card_treasury_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-blue-600 text-white rounded-xl p-8 text-center mb-6 shadow-lg">
                <p class="text-blue-100 mb-2">{{ __('messages.treasury_total_balance_label') }}</p>
                <p class="text-4xl font-bold">{{ number_format($totalBalance, 2) }} {{ __('messages.currency') }}</p>
            </div>

            <div class="flex justify-end gap-2 mb-4">
                <a href="{{ route('admin.treasury.report') }}"
                   class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
                    {{ __('messages.treasury_report_link') }}
                </a>
                <a href="{{ route('admin.treasury.sources') }}"
                   class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
                    {{ __('messages.treasury_manage_sources_link') }}
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($sourcesData as $data)
                    <a href="{{ route('admin.treasury.transactions', $data['source']) }}"
                       class="bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl">
                                @if ($data['source']->type === 'bank') 🏦
                                @elseif ($data['source']->type === 'mobile_app') 📱
                                @else 💵
                                @endif
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                @if ($data['source']->type === 'bank') {{ __('messages.treasury_source_type_bank') }}
                                @elseif ($data['source']->type === 'mobile_app') {{ __('messages.treasury_source_type_mobile_app') }}
                                @else {{ __('messages.treasury_source_type_cash') }}
                                @endif
                            </span>
                        </div>
                        <h3 class="font-bold text-lg mb-1 text-gray-800">{{ $data['source']->name }}</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($data['balance'], 2) }}</p>
                        <p class="text-gray-400 text-xs">{{ __('messages.currency') }}</p>
                    </a>
                @empty
                    <div class="col-span-3 bg-white rounded-xl p-8 text-center text-gray-500">
                        {{ __('messages.treasury_no_sources_empty') }}
                        <a href="{{ route('admin.treasury.sources') }}" class="text-blue-600 hover:underline">{{ __('messages.treasury_add_source_now_link') }}</a>
                    </div>
                @endforelse
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6 mt-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.treasury_breakdown_title') }}</h3>
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.type') }}</th>
                            <th class="p-2">{{ __('messages.treasury_category_label') }}</th>
                            <th class="p-2">{{ __('messages.treasury_breakdown_count_col') }}</th>
                            <th class="p-2">{{ __('messages.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categoryTotals as $row)
                            <tr class="border-b">
                                <td class="p-2">
                                    @if ($row->direction === 'in')
                                        <span class="text-green-600">{{ __('messages.treasury_direction_in_badge') }}</span>
                                    @else
                                        <span class="text-red-600">{{ __('messages.treasury_direction_out_badge') }}</span>
                                    @endif
                                </td>
                                <td class="p-2">{{ $row->category ? __('messages.treasury_category_'.$row->category) : __('messages.treasury_category_uncategorized') }}</td>
                                <td class="p-2">{{ $row->entries_count }}</td>
                                <td class="p-2 font-bold">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">{{ __('messages.treasury_no_transactions_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>