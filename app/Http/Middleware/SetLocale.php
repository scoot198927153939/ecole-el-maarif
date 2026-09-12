<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->locale) {
            app()->setLocale(auth()->user()->locale);
        } elseif (session('locale')) {
            app()->setLocale(session('locale'));
        } else {
            app()->setLocale('ar');
        }

        return $next($request);
    }
}