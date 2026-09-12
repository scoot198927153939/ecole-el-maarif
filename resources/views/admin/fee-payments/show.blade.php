<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.fee_payments_show_title_prefix') }} — {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.fee_payments_discount_title') }}</h3>
                <form method="POST" action="{{ route('admin.fee-payments.discount', $enrollment) }}" class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <label class="block font-medium mb-1">{{ __('messages.fee_payments_discount_percentage_label') }}</label>
                        <input type="number" step="0.01" min="0" max="100" name="discount_percentage"
                               value="{{ $enrollment->discount_percentage }}"
                               class="w-full border rounded-lg p-2">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.fee_payments_update_percentage_button') }}
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    {{ __('messages.fee_payments_original_fee_note') }} {{ number_format($enrollment->tuitionAmount(), 2) }} —
                    {{ __('messages.fee_payments_after_discount_note') }} {{ number_format($enrollment->requiredAmount(), 2) }}
                </p>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-xl p-4 text-center">
                    <p class="text-gray-500 text-sm">{{ __('messages.required_amount') }}</p>
                    <p class="text-xl font-bold">{{ number_format($enrollment->requiredAmount(), 2) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-4 text-center">
                    <p class="text-gray-500 text-sm">{{ __('messages.paid_amount') }}</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($enrollment->paidAmount(), 2) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-4 text-center">
                    <p class="text-gray-500 text-sm">{{ __('messages.remaining_amount') }}</p>
                    <p class="text-xl font-bold text-red-600">{{ number_format($enrollment->remainingAmount(), 2) }}</p>
                </div>
            </div>

            @if ($enrollment->next_payment_due_date)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                    <p class="text-amber-700">
                        {{ __('messages.fee_payments_next_payment_due_note') }}
                        <strong><span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->next_payment_due_date->format('Y-m-d') }}</span></strong>
                    </p>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.fee_payments_new_payment_title') }}</h3>
                <form method="POST" action="{{ route('admin.fee-payments.store', $enrollment) }}" enctype="multipart/form-data" x-data="{ method: 'cash' }">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.amount') }}</label>
                            <input type="number" step="0.01" name="amount" class="w-full border rounded-lg p-2" required>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.fee_payments_payment_date_label') }}</label>
                            <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" class="w-full border rounded-lg p-2" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.fee_payments_money_source_label') }}</label>
                        <select name="money_source_id" class="w-full border rounded-lg p-2" required>
                            <option value="">{{ __('messages.tuition_fees_select_generic_placeholder') }}</option>
                            @foreach ($moneySources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.fee_payments_method_label') }}</label>
                        <select name="method" x-model="method" class="w-full border rounded-lg p-2" required>
                            <option value="cash">{{ __('messages.fee_payments_method_cash') }}</option>
                            <option value="transfer">{{ __('messages.fee_payments_method_transfer') }}</option>
                        </select>
                    </div>

                    <div x-show="method === 'transfer'" class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-4 space-y-3">
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.fee_payments_service_name_label') }}</label>
                            <input type="text" name="transfer_service" class="w-full border rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.fee_payments_transaction_number_label') }}</label>
                            <input type="text" name="transfer_number" dir="ltr" class="w-full border rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.fee_payments_sender_account_label') }}</label>
                            <input type="text" name="sender_account" dir="ltr" class="w-full border rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.fee_payments_receiver_account_label') }}</label>
                            <input type="text" name="receiver_account" dir="ltr" class="w-full border rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.fee_payments_proof_image_label') }}</label>
                            <input type="file" name="transfer_photo" accept="image/*" class="w-full border rounded-lg p-2">
                        </div>
                    </div>

                    <div class="mb-4 bg-amber-50 border border-amber-100 rounded-lg p-4">
                        <label class="block font-medium mb-1">{{ __('messages.fee_payments_next_due_date_label') }}</label>
                        <input type="date" name="next_payment_due_date" class="w-full border rounded-lg p-2">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.fee_payments_save_payment_button') }}
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.fee_payments_log_title') }}</h3>
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.date') }}</th>
                            <th class="p-2">{{ __('messages.amount') }}</th>
                            <th class="p-2">{{ __('messages.method') }}</th>
                            <th class="p-2">{{ __('messages.fee_payments_col_proof') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollment->feePayments as $payment)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2"><span dir="ltr" style="unicode-bidi: embed;">{{ $payment->payment_date->format('Y-m-d') }}</span></td>
                                <td class="p-2 font-bold">{{ number_format($payment->amount, 2) }}</td>
                                <td class="p-2">{{ $payment->method === 'cash' ? __('messages.fee_payments_method_cash') : __('messages.fee_payments_method_transfer_short') }}</td>
                                <td class="p-2">
                                    @if ($payment->transfer_photo_path)
                                        <a href="{{ asset('storage/'.$payment->transfer_photo_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ __('messages.view') }}</a>
                                    @else - @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">{{ __('messages.fee_payments_no_payments_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>