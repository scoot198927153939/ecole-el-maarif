<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.lesson_logs_create_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('lesson-logs.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.lesson_logs_assignment_label') }}</label>
                        <select name="class_subject_teacher_id" class="w-full border rounded p-2" required>
                            <option value="">{{ __('messages.schedules_select_assignment_placeholder') }}</option>
                            @foreach ($assignments as $assignment)
                                <option value="{{ $assignment->id }}" {{ old('class_subject_teacher_id') == $assignment->id ? 'selected' : '' }}>
                                    {{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }} —
                                    {{ $assignment->subject->name }} —
                                    {{ $assignment->classRoom->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_subject_teacher_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.pdf_lesson_title_label') }}</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full border rounded p-2" required>
                        @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.pdf_lesson_topic_label') }}</label>
                        <textarea name="topic" rows="3" class="w-full border rounded p-2" required>{{ old('topic') }}</textarea>
                        @error('topic') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.pdf_lesson_date_label') }}</label>
                        <input type="date" name="lesson_date" value="{{ old('lesson_date') }}"
                               class="w-full border rounded p-2" required>
                        @error('lesson_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.lesson_logs_camera_capture_label') }}</label>
                        <input type="file" name="photos[]" accept="image/*" capture="environment"
                               class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.lesson_logs_gallery_import_label') }}</label>
                        <input type="file" name="photos[]" accept="image/*" multiple
                               class="w-full border rounded p-2">
                        @error('photos') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        @error('photos.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.lesson_logs_save_button') }}
                        </button>
                        <a href="{{ route('lesson-logs.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>