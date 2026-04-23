<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    // Redirect user to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find user by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'firstName' => $googleUser->user['given_name'] ?? 'User',
                    'lastName'  => $googleUser->user['family_name'] ?? '',
                    'email'     => $googleUser->getEmail(),
                    'password'  => bcrypt(Str::random(16)),
                    'is_questionnaire_completed' => false,
                    'email_verified_at' => now(), // ✅ auto verify
                ]);
            }

            // Ensure profile exists
            $user->profile()->firstOrCreate([
                'user_id' => $user->id,
            ], [
                'full_name' => trim($user->firstName . ' ' . $user->lastName),
                'current_status' => 'undecided',
            ]);

            // Login user
            Auth::login($user);
            request()->session()->regenerate();

            // Bypass OTP
            session(['otp_verified' => true]);

            // Redirect logic
            if (!$user->is_questionnaire_completed) {
                return redirect()->route('onboarding.show');
            }

            return redirect('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')
                ->withErrors(['msg' => 'Google login failed']);
        }
    }
}
