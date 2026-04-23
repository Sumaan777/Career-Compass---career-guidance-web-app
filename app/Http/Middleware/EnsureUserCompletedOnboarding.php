<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserCompletedOnboarding
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {

            $user = Auth::user();

            // 🔥 STRICT OTP MODE:
            // Email is ALWAYS verified at this point
            // So DO NOT check email_verified_at here

            // If user_type missing → force onboarding
            if (empty($user->user_type)) {

                if (
                    !$request->routeIs('onboarding.show') &&
                    !$request->routeIs('onboarding.store')
                ) {
                    return redirect()->route('onboarding.show');
                }
            }
        }

        return $next($request);
    }
}
