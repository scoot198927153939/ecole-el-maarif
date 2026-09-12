<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.card_partners_title') }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-blue-600 text-white rounded-xl p-6 text-center mb-6">
                <p class="text-blue-100 mb-1">{{ __('messages.partners_net_profit_label', ['year' => $currentYear->name ?? '-']) }}</p>
                <p class="text-3xl font-bold">{{ number_format($netProfit, 2) }} {{ __('messages.currency') }}</p>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.partners_list_title') }}</h3>
                    <a href="{{ route('admin.partners.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('messages.partners_add_button') }}</a>
                </div>

                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.percentage') }}</th>
                            <th class="p-2">{{ __('messages.partners_col_entitled') }}</th>
                            <th class="p-2">{{ __('messages.partners_col_total_withdrawn') }}</th>
                            <th class="p-2">{{ __('messages.difference') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partners as $partner)
                            <tr class="border-b hover:bg-gray-50 {{ $partner->over_withdrawn ? 'bg-red-50' : '' }}">
                                <td class="p-2 font-medium">{{ $partner->name }}</td>
                                <td class="p-2">{{ $partner->percentage }}%</td>
                                <td class="p-2">{{ number_format($partner->entitled_amount, 2) }}</td>
                                <td class="p-2">{{ number_format($partner->total_withdrawn, 2) }}</td>
                                <td class="p-2 font-bold {{ $partner->over_withdrawn ? 'text-red-600' : 'text-green-600' }}">
                                    @if ($partner->over_withdrawn)
                                        {{ __('messages.partners_over_withdrawn_prefix') }} {{ number_format(abs($partner->difference), 2) }}
                                    @else
                                        {{ number_format($partner->difference, 2) }}
                                    @endif
                                </td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.partners.show', $partner) }}" class="text-blue-600 hover:underline">{{ __('messages.late_payments_details_link') }}</a>
                                    <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" class="inline"
                                          onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">{{ __('messages.partners_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>