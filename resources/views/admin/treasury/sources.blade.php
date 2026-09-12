<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.treasury_sources_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('admin.treasury.index') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
                {{ __('messages.treasury_back_to_treasury_link') }}
            </a>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.treasury_add_source_title') }}</h3>
                <form method="POST" action="{{ route('admin.treasury.sources.store') }}" class="flex gap-3">
                    @csrf
                    <input type="text" name="name" placeholder="{{ __('messages.treasury_source_name_placeholder') }}"
                           class="flex-1 border rounded-lg p-2" required>
                    <select name="type" class="border rounded-lg p-2" required>
                        <option value="bank">{{ __('messages.treasury_source_type_bank') }}</option>
                        <option value="mobile_app">{{ __('messages.treasury_source_type_mobile_app') }}</option>
                        <option value="cash">{{ __('messages.treasury_source_type_cash') }}</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.add') }}
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('messages.treasury_current_sources_title') }}</h3>
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.type') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sources as $source)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $source->name }}</td>
                                <td class="p-2">
                                    @if ($source->type === 'bank') {{ __('messages.treasury_source_type_bank') }}
                                    @elseif ($source->type === 'mobile_app') {{ __('messages.treasury_source_type_mobile_app') }}
                                    @else {{ __('messages.treasury_source_type_cash') }}
                                    @endif
                                </td>
                                <td class="p-2">
                                    <form action="{{ route('admin.treasury.sources.destroy', $source) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('{{ addslashes(__('messages.treasury_delete_source_confirm')) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-500">{{ __('messages.treasury_no_sources_short_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>