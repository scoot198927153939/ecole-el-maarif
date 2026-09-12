<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.treasury_transactions_title_prefix') }} — {{ $source->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('admin.treasury.index') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.treasury_back_to_treasury_link') }}
            </a>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-blue-600 text-white rounded-xl p-6 text-center mb-6">
                <p class="text-blue-100 mb-1">{{ __('messages.treasury_current_balance_label') }}</p>
                <p class="text-3xl font-bold">{{ number_format($source->balance(), 2) }} {{ __('messages.currency') }}</p>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.treasury_new_transaction_title') }}</h3>
                <form method="POST" action="{{ route('admin.treasury.transactions.store', $source) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.type') }}</label>
                            <select name="direction" class="w-full border rounded-lg p-2" required>
                                <option value="in">{{ __('messages.treasury_direction_in') }}</option>
                                <option value="out">{{ __('messages.treasury_direction_out') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.amount') }}</label>
                            <input type="number" step="0.01" name="amount" class="w-full border rounded-lg p-2" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.description') }}</label>
                        <input type="text" name="description" class="w-full border rounded-lg p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.date') }}</label>
                        <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}"
                               class="w-full border rounded-lg p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.treasury_document_label') }}</label>
                        <input type="file" name="document" accept="image/*" class="w-full border rounded-lg p-2">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.treasury_save_transaction_button') }}
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.treasury_transactions_log_title') }}</h3>
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.date') }}</th>
                            <th class="p-2">{{ __('messages.type') }}</th>
                            <th class="p-2">{{ __('messages.amount') }}</th>
                            <th class="p-2">{{ __('messages.description') }}</th>
                            <th class="p-2">{{ __('messages.treasury_col_document') }}</th>
                            <th class="p-2">{{ __('messages.treasury_col_recorded_by') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2"><span dir="ltr" style="unicode-bidi: embed;">{{ $t->transaction_date->format('Y-m-d') }}</span></td>
                                <td class="p-2">
                                    @if ($t->direction === 'in')
                                        <span class="text-green-600">{{ __('messages.treasury_direction_in_badge') }}</span>
                                    @else
                                        <span class="text-red-600">{{ __('messages.treasury_direction_out_badge') }}</span>
                                    @endif
                                </td>
                                <td class="p-2 font-bold">{{ number_format($t->amount, 2) }}</td>
                                <td class="p-2">{{ $t->description }}</td>
                                <td class="p-2">
                                    @if ($t->document_path)
                                        <a href="{{ asset('storage/'.$t->document_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ __('messages.view') }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-2">{{ $t->recordedBy->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">{{ __('messages.treasury_no_transactions_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>