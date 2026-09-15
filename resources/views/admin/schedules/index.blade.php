<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.schedules_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">{{ __('messages.schedules_list_title') }}</h3>
                <a href="{{ route('admin.schedules.master') }}"
                   class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">
                    {{ __('messages.schedules_master_link') }}
                </a>
            </div>

            @if ($classes->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    {{ __('messages.schedules_empty') }}
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($classes as $class)
                        <a href="{{ route('admin.schedules.show', $class) }}"
                           class="group bg-white border border-gray-100 shadow-sm rounded-xl p-5 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">🕐</div>
                            <h4 class="font-bold text-gray-800">{{ $class->name }}</h4>
                            <p class="text-gray-500 text-sm mt-1">{{ $class->academicYear->name ?? '-' }}</p>
                            <p class="text-gray-400 text-xs mt-2">{{ __('messages.schedules_col_session_count') }}: {{ $class->schedules_count }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
