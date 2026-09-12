<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.gradebook_title') }} — {{ $class->name }} {{ __('messages.gradebook_select_subject_suffix') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('gradebook.classes') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.gradebook_back_to_classes_link') }}
            </a>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                @forelse ($subjects as $subject)
                    <a href="{{ route('gradebook.grid', [$class, $subject]) }}"
                       class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition text-center">
                        <h3 class="font-bold text-lg mb-1">{{ $subject->name }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('messages.coefficient') }}: {{ $subject->coefficient }}</p>
                    </a>
                @empty
                    <p class="text-gray-500 col-span-3 text-center py-6">
                        {{ __('messages.gradebook_no_subjects_empty') }}
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>