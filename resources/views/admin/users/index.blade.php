<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.card_users_title') }}
        </h2>
    </x-slot>

    <div class="py-12" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ __('messages.users_list_title') }}</h3>
                    <a href="{{ route('admin.users.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ __('messages.users_add_button') }}
                    </a>
                </div>

                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">{{ __('messages.name') }}</th>
                            <th class="p-2">{{ __('messages.email') }}</th>
                            <th class="p-2">{{ __('messages.role') }}</th>
                            <th class="p-2">{{ __('messages.users_col_created_at') }}</th>
                            <th class="p-2">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $user->name }}</td>
                                <td class="p-2"><span dir="ltr" style="unicode-bidi: embed;">{{ $user->email }}</span></td>
                                <td class="p-2">
                                    @if ($user->role === 'admin')
                                        <span class="text-purple-600">{{ __('messages.users_role_admin') }}</span>
                                    @elseif ($user->role === 'supervisor')
                                        <span class="text-blue-600">{{ __('messages.users_role_supervisor') }}</span>
                                    @else
                                        <span class="text-green-600">{{ __('messages.users_role_teacher') }}</span>
                                    @endif
                                </td>
                                <td class="p-2">{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="p-2 space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-blue-600 hover:underline">{{ __('messages.edit') }}</a>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('{{ addslashes(__('messages.users_delete_account_confirm')) }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">{{ __('messages.delete') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">
                                    {{ __('messages.users_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>