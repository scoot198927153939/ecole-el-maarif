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
                        <label class="block font-medium mb-1">{{ __('messages.treasury_category_label') }}</label>
                        <select name="category" class="w-full border rounded-lg p-2" required>
                            <option value="">{{ __('messages.tuition_fees_select_generic_placeholder') }}</option>
                            <optgroup label="{{ __('messages.treasury_direction_in_badge') }}">
                                @foreach (\App\Models\MoneyTransaction::MANUAL_CATEGORIES['in'] as $cat)
                                    <option value="{{ $cat }}">{{ __('messages.treasury_category_'.$cat) }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="{{ __('messages.treasury_direction_out_badge') }}">
                                @foreach (\App\Models\MoneyTransaction::MANUAL_CATEGORIES['out'] as $cat)
                                    <option value="{{ $cat }}">{{ __('messages.treasury_category_'.$cat) }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('category') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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
                            <th class="p-2">{{ __('messages.treasury_category_label') }}</th>
                            <th class="p-2">{{ __('messages.description') }}</th>
                            <th class="p-2">{{ __('messages.treasury_col_document') }}</th>
                            <th class="p-2">{{ __('messages.treasury_col_recorded_by') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
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
                                <td class="p-2">{{ $t->category_label }}</td>
                                <td class="p-2">{{ $t->description }}</td>
                                <td class="p-2">
                                    @if ($t->document_path)
                                        <a href="{{ asset('storage/'.$t->document_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ __('messages.view') }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-2">{{ $t->recordedBy->name }}</td>
                                <td class="p-2 whitespace-nowrap space-x-2 space-x-reverse">
                                    @if ($t->isManual())
                                        <a href="{{ route('admin.treasury.transactions.edit', [$source, $t]) }}" class="text-green-600 hover:underline">{{ __('messages.edit') }}</a>
                                        <form method="POST" action="{{ route('admin.treasury.transactions.destroy', [$source, $t]) }}" class="inline"
                                              onsubmit="return confirm('{{ addslashes(__('messages.treasury_cancel_confirm')) }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">{{ __('messages.treasury_cancel_button') }}</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('messages.treasury_system_entry_note') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-4 text-center text-gray-500">{{ __('messages.treasury_no_transactions_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6 mt-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.treasury_logs_title') }}</h3>
                @php
                    $fieldLabel = fn ($field) => __('messages.treasury_log_field_'.$field);
                    $fmt = function ($field, $value) {
                        return match ($field) {
                            'category' => $value ? __('messages.treasury_category_'.$value) : __('messages.treasury_category_uncategorized'),
                            'direction' => $value === 'in' ? __('messages.treasury_direction_in_badge') : __('messages.treasury_direction_out_badge'),
                            default => $value ?? '-',
                        };
                    };
                @endphp
                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.date') }}</th>
                            <th class="p-2">{{ __('messages.treasury_log_col_action') }}</th>
                            <th class="p-2">{{ __('messages.treasury_col_recorded_by') }}</th>
                            <th class="p-2">{{ __('messages.description') }}</th>
                            <th class="p-2">{{ __('messages.treasury_log_col_details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-b align-top">
                                <td class="p-2"><span dir="ltr">{{ $log->created_at->format('Y-m-d H:i') }}</span></td>
                                <td class="p-2">
                                    @if ($log->action === 'updated')
                                        <span class="text-amber-600">{{ __('messages.treasury_log_action_updated') }}</span>
                                    @else
                                        <span class="text-red-600">{{ __('messages.treasury_log_action_cancelled') }}</span>
                                    @endif
                                </td>
                                <td class="p-2">{{ $log->user?->name ?? '-' }}</td>
                                <td class="p-2">{{ $log->description }}</td>
                                <td class="p-2">
                                    @if ($log->action === 'updated')
                                        @foreach ($log->details as $field => $change)
                                            <div>{{ $fieldLabel($field) }}: {{ $fmt($field, $change['old']) }} ← {{ $fmt($field, $change['new']) }}</div>
                                        @endforeach
                                    @else
                                        <div>{{ $fieldLabel('amount') }}: {{ $log->details['amount'] ?? '-' }} — {{ $fmt('direction', $log->details['direction'] ?? null) }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">{{ __('messages.treasury_logs_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>