<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.attendance_alerts_page_title') }}
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
                <h3 class="text-lg font-bold mb-4">{{ __('messages.attendance_alerts_list_title') }}</h3>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.date') }}</th>
                            <th class="p-2">{{ __('messages.class') }}</th>
                            <th class="p-2">{{ __('messages.subject') }}</th>
                            <th class="p-2">{{ __('messages.teacher') }}</th>
                            <th class="p-2">{{ __('messages.session') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alerts as $alert)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2"><span dir="ltr" style="unicode-bidi: embed;">{{ $alert->date->format('Y-m-d') }}</span></td>
                                <td class="p-2">{{ $alert->schedule->assignment->classRoom->name }}</td>
                                <td class="p-2">{{ $alert->schedule->assignment->subject->name }}</td>
                                <td class="p-2">{{ $alert->schedule->assignment->teacher->first_name }} {{ $alert->schedule->assignment->teacher->last_name }}</td>
                                <td class="p-2">{{ $alert->schedule->session_number }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">
                                    {{ __('messages.attendance_alerts_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>