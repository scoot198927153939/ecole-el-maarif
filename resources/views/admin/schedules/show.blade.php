<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.schedules_show_title_prefix') }} {{ $class->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('admin.schedules.index') }}" class="text-blue-600 hover:underline text-sm">
                    {{ __('messages.schedules_back_link') }}
                </a>
                <a href="{{ route('admin.schedules.master') }}" class="text-gray-600 hover:underline text-sm">
                    {{ __('messages.schedules_master_link') }}
                </a>
            </div>

            @if ($assignments->isEmpty())
                <div class="bg-white shadow-sm rounded-xl p-6 text-center text-gray-500">
                    {{ __('messages.schedules_no_assignments_note') }}
                </div>
            @else
                <div class="bg-white shadow-sm rounded-xl p-4 overflow-x-auto">
                    <table class="w-full text-center border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-2 text-right">{{ __('messages.session_number') }}</th>
                                @foreach ($days as $dayNum => $dayName)
                                    <th class="p-2">{{ $dayName }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessionTimes as $sessionNum => $times)
                                <tr class="border-b">
                                    <td class="p-2 text-right font-medium text-gray-600 whitespace-nowrap">
                                        {{ $sessionNum }}
                                        <div class="text-xs text-gray-400" dir="ltr">{{ $times[0] }} - {{ $times[1] }}</div>
                                    </td>
                                    @foreach ($days as $dayNum => $dayName)
                                        @php $current = $schedules->get($dayNum.'-'.$sessionNum); @endphp
                                        <td class="p-1 align-top">
                                            <form method="POST" action="{{ route('admin.schedules.slot.store', $class) }}">
                                                @csrf
                                                <input type="hidden" name="day_of_week" value="{{ $dayNum }}">
                                                <input type="hidden" name="session_number" value="{{ $sessionNum }}">
                                                <select name="class_subject_teacher_id" onchange="this.form.requestSubmit()"
                                                        class="w-full text-xs border rounded p-1.5 {{ $current ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                                                    <option value="">{{ __('messages.schedules_slot_empty_option') }}</option>
                                                    @foreach ($assignments as $a)
                                                        <option value="{{ $a->id }}" {{ $current && $current->class_subject_teacher_id === $a->id ? 'selected' : '' }}>
                                                            {{ $a->subject->name }} — {{ $a->teacher->first_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-400 mt-3">{{ __('messages.schedules_grid_hint') }}</p>
            @endif
        </div>
    </div>
</x-app-layout>
