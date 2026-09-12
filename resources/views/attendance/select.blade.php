<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.attendance_select_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('attendance.schedules') }}">
                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.day') }}</label>
                        <select name="day_of_week" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.schedules_select_day_placeholder') }}</option>
                            @foreach ($days as $num => $label)
                                <option value="{{ $num }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ __('messages.attendance_view_day_sessions_button') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>