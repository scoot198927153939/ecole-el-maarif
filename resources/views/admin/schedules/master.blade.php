<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.schedules_master_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('admin.schedules.index') }}" class="text-blue-600 hover:underline text-sm inline-block mb-4">
                {{ __('messages.schedules_back_link') }}
            </a>

            @if ($classes->isEmpty())
                <div class="bg-white shadow-sm rounded-xl p-6 text-center text-gray-500">
                    {{ __('messages.schedules_empty') }}
                </div>
            @else
                <div class="bg-white shadow-sm rounded-xl p-4 overflow-x-auto">
                    <table class="w-full text-center border-collapse text-xs whitespace-nowrap">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2 text-right sticky right-0 bg-gray-50">{{ __('messages.pdf_section_label') }}</th>
                                @foreach ($days as $dayNum => $dayName)
                                    <th class="p-2 border-s" colspan="{{ count($sessionTimes) }}">{{ $dayName }}</th>
                                @endforeach
                            </tr>
                            <tr class="border-b bg-gray-50 text-gray-400">
                                <th class="p-1 text-right sticky right-0 bg-gray-50"></th>
                                @foreach ($days as $dayNum => $dayName)
                                    @foreach ($sessionTimes as $sessionNum => $times)
                                        <th class="p-1 border-s font-normal" dir="ltr">{{ $times[0] }}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $class)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2 text-right font-bold sticky right-0 bg-white">{{ $class->name }}</td>
                                    @foreach ($days as $dayNum => $dayName)
                                        @foreach ($sessionTimes as $sessionNum => $times)
                                            @php $s = $schedules->get($class->id.'-'.$dayNum.'-'.$sessionNum); @endphp
                                            <td class="p-1 border-s">
                                                @if ($s)
                                                    <div class="font-medium">{{ $s->assignment->subject->name }}</div>
                                                    <div class="text-gray-400">{{ $s->assignment->teacher->first_name }}</div>
                                                @else
                                                    <span class="text-gray-300">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
