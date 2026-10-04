<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.treasury_report_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('admin.treasury.index') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.treasury_back_to_treasury_link') }}
            </a>

            <div class="bg-white shadow-sm rounded-xl p-6 mb-6">
                <form method="GET" action="{{ route('admin.treasury.report') }}" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('messages.treasury_report_year_label') }}</label>
                        <input type="number" name="year" value="{{ $year }}" class="border rounded-lg p-2 w-28">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('messages.treasury_report_source_label') }}</label>
                        <select name="money_source_id" class="border rounded-lg p-2">
                            <option value="">{{ __('messages.treasury_report_all_sources') }}</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}" @selected($sourceId === $source->id)>{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('messages.view') }}</button>
                    <a href="{{ route('admin.treasury.report.export', ['year' => $year, 'money_source_id' => $sourceId]) }}"
                       class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">{{ __('messages.treasury_report_export_button') }}</a>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.treasury_report_col_month') }}</th>
                            <th class="p-2">{{ __('messages.treasury_report_col_income') }}</th>
                            <th class="p-2">{{ __('messages.treasury_report_col_expenses') }}</th>
                            <th class="p-2">{{ __('messages.treasury_report_col_partners') }}</th>
                            <th class="p-2">{{ __('messages.treasury_report_col_net') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($months as $month => $row)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2"><span dir="ltr">{{ sprintf('%02d/%d', $month, $year) }}</span></td>
                                <td class="p-2 text-green-600">{{ number_format($row['income'], 2) }}</td>
                                <td class="p-2 text-red-600">{{ number_format($row['expenses'], 2) }}</td>
                                <td class="p-2 text-amber-600">{{ number_format($row['partners'], 2) }}</td>
                                <td class="p-2 font-bold {{ $row['net'] < 0 ? 'text-red-600' : 'text-blue-700' }}">{{ number_format($row['net'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-bold">
                            <td class="p-2">{{ __('messages.treasury_report_total_row') }}</td>
                            <td class="p-2 text-green-600">{{ number_format($totals['income'], 2) }}</td>
                            <td class="p-2 text-red-600">{{ number_format($totals['expenses'], 2) }}</td>
                            <td class="p-2 text-amber-600">{{ number_format($totals['partners'], 2) }}</td>
                            <td class="p-2 {{ $totals['net'] < 0 ? 'text-red-600' : 'text-blue-700' }}">{{ number_format($totals['net'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <p class="text-xs text-gray-500 mt-4">{{ __('messages.treasury_report_note') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
