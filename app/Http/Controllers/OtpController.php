<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class OtpController extends Controller
{
    /* =====================================================
       STEP 1: SHOW OTP FORM (SIGNUP ONLY)
    ====================================================== */
    public function showOtpForm()
    {
        if (!session()->has('pending_user') || !session()->has('pending_otp')) {
            return redirect()->route('signup')
                ->with('error', 'Please register first.');
        }

        return view('auth.verify-otp');
    }

    /* =====================================================
       STEP 2: VERIFY OTP & CREATE USER
    ====================================================== */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $pendingUser = session('pending_user');
        $pendingOtp  = session('pending_otp');

        if (!$pendingUser || !$pendingOtp) {
            return redirect()->route('signup')
                ->with('error', 'Session expired. Please register again.');
        }

        // ❌ Invalid OTP
        if ($pendingOtp['code'] != $request->otp) {
            return back()->with('error', 'Invalid OTP.');
        }

        // ❌ Expired OTP
        if (now()->gt($pendingOtp['expires_at'])) {
            return back()->with('error', 'OTP expired.');
        }

        /* =====================================================
           CREATE USER AFTER OTP VERIFIED
        ====================================================== */
        $user = User::create([
            'first_name'        => $pendingUser['first_name'],
            'last_name'         => $pendingUser['last_name'],
            'name'              => $pendingUser['name'],
            'email'             => $pendingUser['email'],
            'password'          => $pendingUser['password'],
            'email_verified_at' => now(),
            'user_type'         => null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // 🧹 CLEAN TEMP SIGNUP DATA
        session()->forget([
            'pending_user',
            'pending_otp'
        ]);

        return redirect()->route('otp.signup.success');
    }

    /* =====================================================
       STEP 3: RESEND OTP (SIGNUP ONLY)
    ====================================================== */
    public function resendOtp()
    {
        $pendingUser = session('pending_user');

        if (!$pendingUser) {
            return redirect()->route('signup');
        }

        $otp = rand(100000, 999999);

        session([
            'pending_otp' => [
                'code'       => $otp,
                'expires_at' => now()->addMinutes(10),
            ]
        ]);

        Mail::to($pendingUser['email'])->send(new SendOtpMail($otp));

        return back()->with('success', 'New OTP sent to your email.');
    }
}
