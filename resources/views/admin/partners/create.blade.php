<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.partners_create_title') }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">

                <form method="POST" action="{{ route('admin.partners.store') }}">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $academicYear->id ?? '' }}">

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.partners_is_teacher_label') }}</label>
                        <select name="teacher_id" id="teacher-select" class="w-full border rounded-lg p-2">
                            <option value="">{{ __('messages.partners_not_teacher_option') }}</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" data-name="{{ $teacher->first_name }} {{ $teacher->last_name }}">
                                    {{ $teacher->first_name }} {{ $teacher->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.partners_name_label') }}</label>
                        <input type="text" name="name" id="name-input" class="w-full border rounded-lg p-2" required>
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.partners_percentage_label') }}</label>
                        <input type="number" step="0.01" min="0.01" max="100" name="percentage" class="w-full border rounded-lg p-2" required>
                        @error('percentage') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('messages.save') }}</button>
                        <a href="{{ route('admin.partners.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('teacher-select').addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const nameInput = document.getElementById('name-input');
            if (this.value) {
                nameInput.value = selected.dataset.name;
            }
        });
    </script>
</x-app-layout>