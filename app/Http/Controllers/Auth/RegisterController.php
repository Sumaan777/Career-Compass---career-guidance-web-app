<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName'  => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', 'confirmed'],
        ]);
    
        // 🔒 STEP 1: Save user data TEMPORARILY (SESSION)
        session([
            'pending_user' => [
                'first_name' => $request->firstName,
                'last_name'  => $request->lastName,
                'name'       => $request->firstName.' '.$request->lastName,
                'email'      => $request->email,
                'password'   => bcrypt($request->password),
                'user_type'  => null,
            ]
        ]);
    
        // 🔐 STEP 2: Generate OTP
        $otp = rand(100000, 999999);
    
        session([
            'pending_otp' => [
                'code' => $otp,
                'expires_at' => now()->addMinutes(10),
            ]
        ]);
    
        // 📧 STEP 3: Send OTP
        Mail::to($request->email)->send(new SendOtpMail($otp));
    
        return redirect()->route('otp.form')
            ->with('success', 'OTP sent to your email.');
    }
    
}
