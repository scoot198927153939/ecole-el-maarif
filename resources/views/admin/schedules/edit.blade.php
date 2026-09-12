<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.schedules_edit_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.day') }}</label>
                        <select name="day_of_week" class="w-full border rounded p-2" required>
                            @foreach ($days as $num => $label)
                                <option value="{{ $num }}" {{ old('day_of_week', $schedule->day_of_week) == $num ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('day_of_week') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.session_number') }}</label>
                        <select name="session_number" class="w-full border rounded p-2" required>
                            <option value="1" {{ old('session_number', $schedule->session_number) == '1' ? 'selected' : '' }}>{{ __('messages.schedules_session_1') }}</option>
                            <option value="2" {{ old('session_number', $schedule->session_number) == '2' ? 'selected' : '' }}>{{ __('messages.schedules_session_2') }}</option>
                            <option value="3" {{ old('session_number', $schedule->session_number) == '3' ? 'selected' : '' }}>{{ __('messages.schedules_session_3') }}</option>
                        </select>
                        @error('session_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.start_time') }}</label>
                        <input type="time" name="start_time"
                               value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}"
                               class="w-full border rounded p-2" required>
                        @error('start_time') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.end_time') }}</label>
                        <input type="time" name="end_time"
                               value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}"
                               class="w-full border rounded p-2" required>
                        @error('end_time') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.schedules_assignment_label') }}</label>
                        <select name="class_subject_teacher_id" class="w-full border rounded p-2" required>
                            @foreach ($assignments as $assignment)
                                <option value="{{ $assignment->id }}"
                                    {{ old('class_subject_teacher_id', $schedule->class_subject_teacher_id) == $assignment->id ? 'selected' : '' }}>
                                    {{ $assignment->classRoom->name }} —
                                    {{ $assignment->subject->name }} —
                                    {{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_subject_teacher_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.update') }}
                        </button>
                        <a href="{{ route('admin.schedules.index') }}"
                           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>