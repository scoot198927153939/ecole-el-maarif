<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.treasury_edit_title') }} — {{ $source->name }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.treasury.transactions', $source) }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.back') }}
            </a>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <form method="POST" action="{{ route('admin.treasury.transactions.update', [$source, $transaction]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.type') }}</label>
                            <select name="direction" class="w-full border rounded-lg p-2" required>
                                <option value="in" @selected(old('direction', $transaction->direction) === 'in')>{{ __('messages.treasury_direction_in') }}</option>
                                <option value="out" @selected(old('direction', $transaction->direction) === 'out')>{{ __('messages.treasury_direction_out') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('messages.amount') }}</label>
                            <input type="number" step="0.01" name="amount" value="{{ old('amount', $transaction->amount) }}" class="w-full border rounded-lg p-2" required>
                            @error('amount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.treasury_category_label') }}</label>
                        <select name="category" class="w-full border rounded-lg p-2" required>
                            <option value="">{{ __('messages.tuition_fees_select_generic_placeholder') }}</option>
                            <optgroup label="{{ __('messages.treasury_direction_in_badge') }}">
                                @foreach (\App\Models\MoneyTransaction::MANUAL_CATEGORIES['in'] as $cat)
                                    <option value="{{ $cat }}" @selected(old('category', $transaction->category) === $cat)>{{ __('messages.treasury_category_'.$cat) }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="{{ __('messages.treasury_direction_out_badge') }}">
                                @foreach (\App\Models\MoneyTransaction::MANUAL_CATEGORIES['out'] as $cat)
                                    <option value="{{ $cat }}" @selected(old('category', $transaction->category) === $cat)>{{ __('messages.treasury_category_'.$cat) }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('category') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.description') }}</label>
                        <input type="text" name="description" value="{{ old('description', $transaction->description) }}" class="w-full border rounded-lg p-2" required>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ __('messages.date') }}</label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}"
                               class="w-full border rounded-lg p-2" required>
                        @error('transaction_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        @if ($transaction->document_path)
                            <p class="text-sm mb-1">
                                {{ __('messages.treasury_current_document_label') }}:
                                <a href="{{ asset('storage/'.$transaction->document_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ __('messages.view') }}</a>
                            </p>
                        @endif
                        <label class="block font-medium mb-1">{{ __('messages.treasury_replace_document_label') }}</label>
                        <input type="file" name="document" accept="image/*" class="w-full border rounded-lg p-2">
                        @error('document') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('messages.treasury_save_changes_button') }}</button>
                        <a href="{{ route('admin.treasury.transactions', $source) }}" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
