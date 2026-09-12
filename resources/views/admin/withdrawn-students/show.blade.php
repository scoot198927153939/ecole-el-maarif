<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.withdrawn_students_show_title_prefix') }} {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="font-bold mb-3">{{ __('messages.withdrawn_students_info_title') }}</h3>
                <table class="w-full text-right border-collapse text-sm">
                    <tbody>
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50 w-1/3">{{ __('messages.student') }}</th>
                            <td class="p-2">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }} ({{ $enrollment->student->student_number }})</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50">{{ __('messages.class') }}</th>
                            <td class="p-2">{{ $enrollment->classRoom->name }}</td>
                        </tr>
                        @if ($enrollment->student->guardian)
                            <tr class="border-b">
                                <th class="p-2 bg-gray-50">{{ __('messages.guardian') }}</th>
                                <td class="p-2">
                                    {{ $enrollment->student->guardian->name }} —
                                    <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->student->guardian->phone1 }}</span>
                                </td>
                            </tr>
                        @endif
                        <tr class="border-b">
                            <th class="p-2 bg-gray-50">{{ __('messages.withdrawn_students_col_paid_total') }}</th>
                            <td class="p-2 font-bold">{{ number_format($enrollment->paidAmount(), 2) }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 bg-gray-50">{{ __('messages.withdrawn_students_col_previously_refunded') }}</th>
                            <td class="p-2">{{ number_format($enrollment->withdrawalRefunds->sum('refund_amount'), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6" x-data="{ method: 'cash' }">
                <h3 class="font-bold mb-4">{{ __('messages.withdrawn_students_new_refund_title') }}</h3>
                <form method="POST" action="{{ route('admin.withdrawn-students.refunds.store', $enrollment) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.withdrawn_students_refund_amount_label') }}</label>
                            <input type="number" step="0.01" name="refund_amount" class="w-full border rounded-lg p-2" required>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.withdrawn_students_refund_date_label') }}</label>
                            <input type="date" name="refund_date" value="{{ now()->format('Y-m-d') }}" class="w-full border rounded-lg p-2" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.withdrawn_students_money_source_label') }}</label>
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
                            <label class="block font-medium mb-1">{{ __('messages.withdrawn_students_receiver_account_label') }}</label>
                            <input type="text" name="receiver_account" dir="ltr" class="w-full border rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.fee_payments_proof_image_label') }}</label>
                            <input type="file" name="transfer_photo" accept="image/*" class="w-full border rounded-lg p-2">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.staff_advances_note_optional_label') }}</label>
                        <textarea name="note" rows="2" class="w-full border rounded-lg p-2"></textarea>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.withdrawn_students_save_issue_receipt_button') }}
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="font-bold mb-4">{{ __('messages.withdrawn_students_refunds_log_title') }}</h3>
                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.date') }}</th>
                            <th class="p-2">{{ __('messages.amount') }}</th>
                            <th class="p-2">{{ __('messages.method') }}</th>
                            <th class="p-2">{{ __('messages.partners_col_note') }}</th>
                            <th class="p-2">{{ __('messages.fee_payments_col_proof') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollment->withdrawalRefunds as $refund)
                            <tr class="border-b">
                                <td class="p-2">{{ $refund->refund_date->format('Y-m-d') }}</td>
                                <td class="p-2 font-bold text-red-600">{{ number_format($refund->refund_amount, 2) }}</td>
                                <td class="p-2">{{ $refund->method === 'cash' ? __('messages.fee_payments_method_cash') : __('messages.fee_payments_method_transfer_short') }}</td>
                                <td class="p-2 text-gray-600">{{ $refund->note ?: '-' }}</td>
                                <td class="p-2">
                                    @if ($refund->transfer_photo_path)
                                        <a href="{{ asset('storage/'.$refund->transfer_photo_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ __('messages.view') }}</a>
                                    @else - @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">{{ __('messages.withdrawn_students_no_refunds_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>