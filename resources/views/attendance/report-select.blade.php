<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.attendance_report_select_class_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="grid grid-cols-1 gap-3">
                    @foreach ($classes as $class)
                        <div class="border rounded p-4 flex justify-between items-center">
                            <span class="font-medium">{{ $class->name }} ({{ $class->academicYear->name }})</span>
                            <div class="flex gap-2">
                                <form method="GET" action="{{ route('attendance.report.daily', $class) }}" class="flex gap-1">
                                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}"
                                           class="border rounded p-1 text-sm">
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        {{ __('messages.attendance_daily_report_button') }}
                                    </button>
                                </form>
                                <form method="GET" action="{{ route('attendance.report.monthly', $class) }}" class="flex gap-1">
                                    <select name="month" class="border rounded p-1 text-sm">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ $m }}</option>
                                        @endfor
                                    </select>
                                    <select name="year" class="border rounded p-1 text-sm">
                                        @for ($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                        {{ __('messages.attendance_monthly_report_button') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>