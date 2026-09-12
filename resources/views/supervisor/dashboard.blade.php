<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.supervisor_dashboard_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if ($grantedModules->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    {{ __('messages.supervisor_dashboard_empty') }}
                </div>
            @else
                <p class="text-gray-500 text-sm mb-4">{{ __('messages.supervisor_dashboard_intro') }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($grantedModules as $module)
                        <a href="{{ route($module['route']) }}"
                           class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">{{ $module['icon'] }}</div>
                            <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.' . $module['label']) }}</h3>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>