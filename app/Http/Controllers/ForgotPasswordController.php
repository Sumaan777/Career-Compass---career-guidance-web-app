<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class ForgotPasswordController extends Controller
{
    /* =====================================================
       STEP 1: SHOW FORGOT PASSWORD EMAIL FORM
    ====================================================== */
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    /* =====================================================
       STEP 2: SEND OTP TO EMAIL
    ====================================================== */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        // 🔒 Security: do not reveal if email exists
        if (!$user) {
            return back()->with(
                'status',
                'If your email exists, an OTP has been sent.'
            );
        }

        // Invalidate old OTPs
        EmailOtp::where('user_id', $user->id)
            ->update(['is_used' => true]);

        $otp = rand(100000, 999999);

        EmailOtp::create([
            'user_id'    => $user->id,
            'otp'        => $otp,
            'is_used'    => false,
            'expires_at'=> now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp));

        // 🔑 Forgot-password specific session
        session([
            'fp_user_id' => $user->id
        ]);

        return redirect()->route('password.otp.form')
            ->with('status', 'OTP sent to your email.');
    }

    /* =====================================================
       STEP 3: SHOW OTP VERIFY FORM
    ====================================================== */
    public function showOtpForm()
    {
        if (!session()->has('fp_user_id')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.verify-forgot-otp');
    }

    /* =====================================================
       STEP 4: VERIFY OTP
    ====================================================== */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $userId = session('fp_user_id');

        if (!$userId) {
            return redirect()->route('password.forgot');
        }

        $otpRecord = EmailOtp::where('user_id', $userId)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->first();

        if (!$otpRecord) {
            return back()->with('error', 'Invalid OTP.');
        }

        if (now()->gt($otpRecord->expires_at)) {
            return back()->with('error', 'OTP expired.');
        }

        $otpRecord->update(['is_used' => true]);

        session([
            'fp_verified' => true
        ]);

        return redirect()->route('password.reset.form');
    }

    /* =====================================================
       STEP 5: SHOW RESET PASSWORD FORM
    ====================================================== */
    public function showResetForm()
    {
        if (!session('fp_verified')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset-password');
    }

    /* =====================================================
       STEP 6: RESET PASSWORD
    ====================================================== */
    public function resetPassword(Request $request)
    {
        if (!session('fp_verified')) {
            return redirect()->route('password.forgot');
        }

        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::find(session('fp_user_id'));

        if (!$user) {
            return redirect()->route('password.forgot');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // 🧹 CLEANUP
        session()->forget([
            'fp_user_id',
            'fp_verified'
        ]);

        return redirect()->route('login')
            ->with('status', 'Password reset successfully. Please login.');
    }
}
