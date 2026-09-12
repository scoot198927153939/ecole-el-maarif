<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.teacher_dashboard_title') }}
        </h2>
        <p class="text-gray-500 text-sm mt-1">{{ __('messages.teacher_dashboard_welcome') }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                <a href="{{ route('gradebook.classes') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📖</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_gradebook_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_gradebook_desc') }}</p>
                </a>

                <a href="{{ route('assessments.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📋</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_assessments_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_assessments_desc') }}</p>
                </a>

                <a href="{{ route('attendance.select') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">✅</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_attendance_take_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_attendance_take_desc') }}</p>
                </a>

                <a href="{{ route('attendance.report.select') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📊</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_attendance_report_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_attendance_report_desc') }}</p>
                </a>

                <a href="{{ route('lesson-logs.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📷</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_lesson_logs_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_lesson_logs_desc') }}</p>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
