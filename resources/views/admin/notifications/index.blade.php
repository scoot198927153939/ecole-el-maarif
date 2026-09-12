<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.notifications_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.notifications_list_title') }}</h3>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.student') }}</th>
                            <th class="p-2">{{ __('messages.subject') }}</th>
                            <th class="p-2">{{ __('messages.assessment') }}</th>
                            <th class="p-2">{{ __('messages.notifications_col_from') }}</th>
                            <th class="p-2">{{ __('messages.notifications_col_to') }}</th>
                            <th class="p-2">{{ __('messages.notifications_col_changed_by') }}</th>
                            <th class="p-2">{{ __('messages.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notification)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">
                                    {{ $notification->grade->enrollment->student->first_name }}
                                    {{ $notification->grade->enrollment->student->last_name }}
                                </td>
                                <td class="p-2">{{ $notification->grade->assessment->subject->name }}</td>
                                <td class="p-2">{{ $notification->grade->assessment->title }}</td>
                                <td class="p-2 text-red-600">{{ $notification->old_score }}</td>
                                <td class="p-2 text-green-600">{{ $notification->new_score }}</td>
                                <td class="p-2">{{ $notification->changedBy->name }}</td>
                                <td class="p-2">{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-gray-500">
                                    {{ __('messages.notifications_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>