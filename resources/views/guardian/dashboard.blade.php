<x-guardian-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.parent_dashboard_welcome') }}، {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.parent_dashboard_title') }}</h3>

                @if ($students->isEmpty())
                    <p class="text-gray-500 text-center py-6">{{ __('messages.parent_no_children_empty') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($students as $student)
                            <a href="{{ route('parent.students.show', $student) }}"
                               class="group border border-gray-100 shadow-sm rounded-xl p-5 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">🎓</div>
                                <h4 class="font-bold text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</h4>
                                <p class="text-gray-500 text-sm mt-1">
                                    {{ $student->current_enrollment?->classRoom?->name ?? '—' }}
                                </p>
                                <span class="inline-block mt-3 text-blue-600 text-sm font-medium">
                                    {{ __('messages.parent_view_child_button') }} ←
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-guardian-layout>
