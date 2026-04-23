<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && is_null(auth()->user()->email_verified_at)) {
            auth()->logout();
            return redirect()->route('otp.form')
                ->with('error', 'Please verify your email first.');
        }
    
        return $next($request);
    }
    
}
