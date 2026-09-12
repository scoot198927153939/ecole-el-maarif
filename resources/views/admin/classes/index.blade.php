<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.classes_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.classes_list_title') }}</h3>
                    <a href="{{ route('admin.classes.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ __('messages.classes_add_button') }}
                    </a>
                </div>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.classes_col_name') }}</th>
                            <th class="p-2">{{ __('messages.grade_level') }}</th>
                            <th class="p-2">{{ __('messages.academic_year') }}</th>
                            <th class="p-2">{{ __('messages.classes_col_full_report') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classes as $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $item->name }}</td>
                                <td class="p-2">{{ $item->grade_level }}</td>
                                <td class="p-2">{{ $item->academicYear->name }}</td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('reports.class.term1.pdf', $item) }}" target="_blank"
                                       class="text-purple-600 hover:underline">{{ __('messages.classes_report_term1') }}</a>
                                    <a href="{{ route('reports.class.term2.pdf', $item) }}" target="_blank"
                                       class="text-purple-600 hover:underline">{{ __('messages.classes_report_term2') }}</a>
                                    <a href="{{ route('reports.class.term3.pdf', $item) }}" target="_blank"
                                       class="text-purple-600 hover:underline">{{ __('messages.classes_report_term3') }}</a>
                                </td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.classes.edit', $item) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                                    <form action="{{ route('admin.classes.destroy', $item) }}"
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
                                    {{ __('messages.classes_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>