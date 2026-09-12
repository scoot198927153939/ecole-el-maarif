<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.card_fee_payments_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-xl p-6">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.student') }}</th>
                            <th class="p-2">{{ __('messages.class') }}</th>
                            <th class="p-2">{{ __('messages.required_amount') }}</th>
                            <th class="p-2">{{ __('messages.paid_amount') }}</th>
                            <th class="p-2">{{ __('messages.remaining_amount') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
                                <td class="p-2">{{ $enrollment->classRoom->name }}</td>
                                <td class="p-2">{{ number_format($enrollment->requiredAmount(), 2) }}</td>
                                <td class="p-2 text-green-600">{{ number_format($enrollment->paidAmount(), 2) }}</td>
                                <td class="p-2 {{ $enrollment->remainingAmount() > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                                    {{ number_format($enrollment->remainingAmount(), 2) }}
                                </td>
                                <td class="p-2">
                                    <a href="{{ route('admin.fee-payments.show', $enrollment) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.fee_payments_details_link') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">{{ __('messages.fee_payments_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>