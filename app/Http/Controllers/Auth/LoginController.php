<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors([
                'email' => 'Invalid email or password.'
            ]);
        }
    
        $user = Auth::user();
    
        // ✅ NO OTP CHECK HERE IN STRICT MODE
    
        // Onboarding check
        if (auth()->user()->user_type === null) {
            return redirect()->route('onboarding.show');
        }
        
        return redirect()->route('dashboard');
        
    }
    


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
