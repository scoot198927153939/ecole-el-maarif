<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.late_payments_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @forelse ($grouped as $guardianId => $items)
                @php
                    $guardian = $items->first()->student->guardian;
                @endphp
                <div class="bg-white shadow-sm rounded-xl p-6">
                    @if ($guardian)
                        <div class="mb-4 pb-4 border-b">
                            <h3 class="text-lg font-bold text-blue-700">{{ $guardian->name }}</h3>
                            <div class="text-sm text-gray-600 flex flex-wrap gap-4 mt-1">
                                <span>📞 <span dir="ltr" style="unicode-bidi: embed;">{{ $guardian->phone1 }}</span></span>
                                <span>{{ __('messages.late_payments_whatsapp_prefix') }} <span dir="ltr" style="unicode-bidi: embed;">{{ $guardian->whatsapp }}</span></span>
                                @if ($guardian->phone2)
                                    <span>📞 <span dir="ltr" style="unicode-bidi: embed;">{{ $guardian->phone2 }}</span></span>
                                @endif
                                @if ($guardian->profession)
                                    <span>💼 {{ $guardian->profession }}</span>
                                @endif
                                @if ($guardian->address)
                                    <span>📍 {{ $guardian->address }}</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <h3 class="text-lg font-bold text-gray-500 mb-4">{{ __('messages.late_payments_no_guardian_title') }}</h3>
                    @endif

                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2">{{ __('messages.student') }}</th>
                                <th class="p-2">{{ __('messages.class') }}</th>
                                <th class="p-2">{{ __('messages.remaining_amount') }}</th>
                                <th class="p-2">{{ __('messages.late_payments_next_payment_col') }}</th>
                                <th class="p-2">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $enrollment)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
                                    <td class="p-2">{{ $enrollment->classRoom->name }}</td>
                                    <td class="p-2 text-red-600 font-bold">{{ number_format($enrollment->remainingAmount(), 2) }}</td>
                                    <td class="p-2">
                                        @if ($enrollment->next_payment_due_date)
                                            @php
                                                $isOverdue = $enrollment->next_payment_due_date->isPast();
                                            @endphp
                                            <span class="{{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                <span dir="ltr" style="unicode-bidi: embed;">{{ $enrollment->next_payment_due_date->format('Y-m-d') }}</span>
                                                @if ($isOverdue) {{ __('messages.late_payments_overdue_badge') }} @endif
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        <a href="{{ route('admin.fee-payments.show', $enrollment) }}"
                                           class="text-blue-600 hover:underline">{{ __('messages.late_payments_details_link') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="bg-white shadow-sm rounded-xl p-8 text-center text-gray-500">
                    {{ __('messages.late_payments_empty') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>