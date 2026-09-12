<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (! in_array($locale, ['ar', 'fr'])) {
            abort(404);
        }

        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        session(['locale' => $locale]);

        app()->setLocale($locale);

        return back();
    }
}