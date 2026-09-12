<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.card_academic_years_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.academic_years_list_title') }}</h3>
                    <a href="{{ route('admin.academic-years.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ __('messages.academic_years_add_button') }}
                    </a>
                </div>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.start_date') }}</th>
                            <th class="p-2">{{ __('messages.end_date') }}</th>
                            <th class="p-2">{{ __('messages.academic_years_col_is_current') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($academicYears as $year)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $year->name }}</td>
                                <td class="p-2">{{ $year->start_date->format('Y-m-d') }}</td>
                                <td class="p-2">{{ $year->end_date->format('Y-m-d') }}</td>
                                <td class="p-2">
                                    @if ($year->is_current)
                                        <span class="text-green-600 font-bold">✔ {{ __('messages.yes') }}</span>
                                    @else
                                        <span class="text-gray-400">{{ __('messages.no') }}</span>
                                    @endif
                                </td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.academic-years.edit', $year) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                                    <form action="{{ route('admin.academic-years.destroy', $year) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">
                                    {{ __('messages.academic_years_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>