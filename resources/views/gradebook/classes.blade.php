<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.gradebook_select_class_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($classes as $class)
                    <a href="{{ route('gradebook.subjects', $class) }}"
                       class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition text-center">
                        <h3 class="font-bold text-lg mb-1">{{ $class->name }}</h3>
                        <p class="text-gray-500 text-sm">{{ $class->academicYear->name }}</p>
                    </a>
                @empty
                    <p class="text-gray-500 col-span-3 text-center py-6">{{ __('messages.gradebook_no_classes_empty') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>