<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ __('messages.staff_advances_create_title') }}</h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">

                <form method="POST" action="{{ route('admin.staff-advances.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.type') }}</label>
                        <select name="staff_type" id="staff-type" class="w-full border rounded-lg p-2" required>
                            <option value="teacher">{{ __('messages.users_role_teacher') }}</option>
                            <option value="staff_member">{{ __('messages.staff_advances_staff_type_staff_member') }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.name') }}</label>
                        <select name="staff_id" id="teacher-select" class="w-full border rounded-lg p-2">
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                            @endforeach
                        </select>
                        <select name="staff_id" id="staff-member-select" class="w-full border rounded-lg p-2" style="display:none">
                            @foreach ($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->first_name }} {{ $staff->last_name }}</option>
                            @endforeach
                        </select>
                        @error('staff_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.staff_advances_amount_label') }}</label>
                        <input type="number" step="0.01" name="amount" class="w-full border rounded-lg p-2" required>
                        @error('amount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.staff_advances_date_given_label') }}</label>
                        <input type="date" name="date_given" value="{{ now()->format('Y-m-d') }}" class="w-full border rounded-lg p-2" required>
                        @error('date_given') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.payout_money_source_label') }}</label>
                        <select name="money_source_id" class="w-full border rounded-lg p-2" required>
                            <option value="">{{ __('messages.tuition_fees_select_generic_placeholder') }}</option>
                            @foreach ($moneySources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        </select>
                        @error('money_source_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.staff_advances_note_optional_label') }}</label>
                        <textarea name="note" rows="2" class="w-full border rounded-lg p-2"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('messages.save') }}</button>
                        <a href="{{ route('admin.staff-advances.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const typeSelect = document.getElementById('staff-type');
        const teacherSelect = document.getElementById('teacher-select');
        const staffSelect = document.getElementById('staff-member-select');

        function toggleSelects() {
            if (typeSelect.value === 'teacher') {
                teacherSelect.style.display = 'block';
                teacherSelect.name = 'staff_id';
                staffSelect.style.display = 'none';
                staffSelect.removeAttribute('name');
            } else {
                staffSelect.style.display = 'block';
                staffSelect.name = 'staff_id';
                teacherSelect.style.display = 'none';
                teacherSelect.removeAttribute('name');
            }
        }

        typeSelect.addEventListener('change', toggleSelects);
        toggleSelects();
    </script>
</x-app-layout>