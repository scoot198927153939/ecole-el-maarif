<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.card_assessments_title') }}
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
                    <h3 class="text-lg font-bold">{{ __('messages.assessments_list_title') }}</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('assessments.bulk-create') }}"
                           class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                            {{ __('messages.assessments_bulk_create_button') }}
                        </a>
                        <a href="{{ route('assessments.create') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.assessments_add_button') }}
                        </a>
                    </div>
                </div>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.title') }}</th>
                            <th class="p-2">{{ __('messages.type') }}</th>
                            <th class="p-2">{{ __('messages.pdf_term_label') }}</th>
                            <th class="p-2">{{ __('messages.subject') }}</th>
                            <th class="p-2">{{ __('messages.class') }}</th>
                            <th class="p-2">{{ __('messages.academic_year') }}</th>
                            <th class="p-2">{{ __('messages.coefficient') }}</th>
                            <th class="p-2">{{ __('messages.date') }}</th>
                            <th class="p-2">{{ __('messages.assessments_col_created_by') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assessments as $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $item->title }}</td>
                                <td class="p-2">
                                    @if ($item->type === 'test')
                                        <span class="text-blue-600">{{ __('messages.assessments_type_test') }}</span>
                                    @else
                                        <span class="text-purple-600">{{ __('messages.assessments_type_exam') }}</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    @if ($item->term == 1) {{ __('messages.pdf_term_1') }}
                                    @elseif ($item->term == 2) {{ __('messages.pdf_term_2') }}
                                    @else {{ __('messages.pdf_term_3') }}
                                    @endif
                                </td>
                                <td class="p-2">{{ $item->subject->name }}</td>
                                <td class="p-2">{{ $item->classRoom->name }}</td>
                                <td class="p-2">{{ $item->academicYear->name }}</td>
                                <td class="p-2">{{ $item->coefficient }}</td>
                                <td class="p-2">{{ $item->assessment_date?->format('Y-m-d') ?? '-' }}</td>
                                <td class="p-2">{{ $item->creator->name }}</td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('grades.index', $item) }}"
                                       class="text-green-600 hover:underline">{{ __('messages.assessments_enter_grades_link') }}</a>
                                    <a href="{{ route('assessments.pdf', $item) }}"
                                       target="_blank"
                                       class="text-orange-600 hover:underline">{{ __('messages.assessments_export_pdf_link') }}</a>
                                    <a href="{{ route('assessments.edit', $item) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                                    <form action="{{ route('assessments.destroy', $item) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-4 text-center text-gray-500">
                                    {{ __('messages.assessments_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>