<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.lesson_logs_edit_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('lesson-logs.update', $lessonLog) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.lesson_logs_assignment_label') }}</label>
                        <select name="class_subject_teacher_id" class="w-full border rounded p-2" required>
                            @foreach ($assignments as $assignment)
                                <option value="{{ $assignment->id }}"
                                    {{ old('class_subject_teacher_id', $lessonLog->class_subject_teacher_id) == $assignment->id ? 'selected' : '' }}>
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
                        <input type="text" name="title" value="{{ old('title', $lessonLog->title) }}"
                               class="w-full border rounded p-2" required>
                        @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.pdf_lesson_topic_label') }}</label>
                        <textarea name="topic" rows="3" class="w-full border rounded p-2" required>{{ old('topic', $lessonLog->topic) }}</textarea>
                        @error('topic') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.pdf_lesson_date_label') }}</label>
                        <input type="date" name="lesson_date"
                               value="{{ old('lesson_date', $lessonLog->lesson_date->format('Y-m-d')) }}"
                               class="w-full border rounded p-2" required>
                        @error('lesson_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if ($lessonLog->photos->count() > 0)
                        <div class="mb-4">
                            <label class="block font-medium mb-2">{{ __('messages.lesson_logs_current_photos_label') }}</label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach ($lessonLog->photos as $photo)
                                    <div class="border rounded p-2 text-center">
                                        <img src="{{ asset('storage/'.$photo->photo_path) }}"
                                             class="w-full h-24 object-cover rounded mb-2">
                                        <button type="button"
                                                onclick="if(confirm('{{ addslashes(__('messages.lesson_logs_delete_photo_confirm')) }}')) document.getElementById('delete-photo-{{ $photo->id }}').submit();"
                                                class="text-red-600 text-sm hover:underline">
                                            {{ __('messages.delete') }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.lesson_logs_camera_capture_new_label') }}</label>
                        <input type="file" name="photos[]" accept="image/*" capture="environment"
                               class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.lesson_logs_gallery_import_additional_label') }}</label>
                        <input type="file" name="photos[]" accept="image/*" multiple
                               class="w-full border rounded p-2">
                        @error('photos') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        @error('photos.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.lesson_logs_save_changes_button') }}
                        </button>
                        <a href="{{ route('lesson-logs.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>

                {{-- نماذج حذف الصور موضوعة هنا، خارج النموذج الرئيسي تمامًا --}}
                @foreach ($lessonLog->photos as $photo)
                    <form id="delete-photo-{{ $photo->id }}"
                          action="{{ route('lesson-logs.photos.destroy', $photo) }}"
                          method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>