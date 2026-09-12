<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.guardians_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('guardian_credentials'))
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-900 rounded-lg">
                    <p class="font-bold mb-2">{{ __('messages.guardian_credentials_flash_title') }}</p>
                    <p>{{ __('messages.guardian_credentials_phone_label') }}: <span dir="ltr" class="font-mono font-bold">{{ session('guardian_credentials')['phone'] }}</span></p>
                    <p>{{ __('messages.guardian_credentials_password_label') }}: <span dir="ltr" class="font-mono font-bold">{{ session('guardian_credentials')['password'] }}</span></p>
                    <p class="text-sm text-blue-700 mt-2">{{ __('messages.guardian_credentials_note') }}</p>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.guardians_list_title') }}</h3>
                    <a href="{{ route('admin.guardians.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.guardians_add_button') }}
                    </a>
                </div>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.profession') }}</th>
                            <th class="p-2">{{ __('messages.students_col_phone1') }}</th>
                            <th class="p-2">{{ __('messages.students_col_whatsapp') }}</th>
                            <th class="p-2">{{ __('messages.guardians_col_students_count') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guardians as $guardian)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $guardian->name }}</td>
                                <td class="p-2">{{ $guardian->profession ?? '-' }}</td>
                                <td class="p-2"><span dir="ltr" style="unicode-bidi: embed;">{{ $guardian->phone1 }}</span></td>
                                <td class="p-2"><span dir="ltr" style="unicode-bidi: embed;">{{ $guardian->whatsapp }}</span></td>
                                <td class="p-2">{{ $guardian->students_count }}</td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.guardians.edit', $guardian) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                                    <form action="{{ route('admin.guardians.destroy', $guardian) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">
                                    {{ __('messages.guardians_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>