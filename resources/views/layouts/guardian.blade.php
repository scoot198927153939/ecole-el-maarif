<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'المدرسة') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Tajawal', sans-serif; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-b from-slate-50 to-blue-50">
            <nav class="bg-white border-b border-blue-100 shadow-sm sticky top-0 z-40">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        <a href="{{ route('parent.dashboard') }}" class="flex items-center gap-2">
                            <div class="bg-blue-600 text-white w-9 h-9 rounded-lg flex items-center justify-center font-bold text-lg">م</div>
                            <span class="font-bold text-gray-800">{{ __('messages.parent_login_page_title') }}</span>
                        </a>

                        <div class="flex items-center gap-2">
                            <div class="flex items-center bg-gray-100 rounded-full p-1 text-sm">
                                <a href="{{ route('locale.switch', 'ar') }}"
                                   class="px-3 py-1 rounded-full transition {{ app()->getLocale() === 'ar' ? 'bg-blue-600 text-white font-bold' : 'text-gray-500 hover:text-gray-700' }}">عربي</a>
                                <a href="{{ route('locale.switch', 'fr') }}"
                                   class="px-3 py-1 rounded-full transition {{ app()->getLocale() === 'fr' ? 'bg-blue-600 text-white font-bold' : 'text-gray-500 hover:text-gray-700' }}">Français</a>
                            </div>

                            <span class="text-sm text-gray-600 hidden sm:inline">{{ Auth::user()->name }}</span>

                            <a href="{{ route('parent.password.edit') }}" class="text-sm text-gray-600 hover:underline px-2">
                                {{ __('messages.guardian_nav_change_password') }}
                            </a>

                            <form method="POST" action="{{ route('parent.logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:underline px-2">
                                    {{ __('messages.parent_logout_button') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            @isset($header)
                <header class="bg-white border-b border-blue-100 shadow-sm">
                    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
