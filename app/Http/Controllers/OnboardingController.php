<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();
            // Clear any pending session data related to onboarding
        session()->forget([
            'pending_user',
            'pending_otp',
            'otp_purpose'
        ]);
        

        if (!$user->email_verified_at) {
            return redirect()->route('otp.form');
        }

        if (!empty($user->user_type)) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.select_type');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:high_school,university,undecided,fresh_grad,switcher'
        ]);

        $user = Auth::user();
        $user->update(['user_type' => $request->user_type]);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome! Your dashboard is ready.');
    }
}
