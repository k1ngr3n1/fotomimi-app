<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if language is specified in query parameter
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if (in_array($locale, ['en', 'hr'])) {
                app()->setLocale($locale);
                session(['locale' => $locale]);
            }
        }
        // Check if language is stored in session
        elseif (session()->has('locale')) {
            $locale = session('locale');
            if (in_array($locale, ['en', 'hr'])) {
                app()->setLocale($locale);
            }
        }
        // Default to Croatian
        else {
            app()->setLocale('hr');
            session(['locale' => 'hr']);
        }

        return $next($request);
    }
}
