<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Allow superadmin to always access
        if ($user->isSuperadmin()) {
            return $next($request);
        }
        
        // Check if user is approved
        if (!$user->isApproved()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is pending approval. Please contact the administrator.');
        }

        return $next($request);
    }
}