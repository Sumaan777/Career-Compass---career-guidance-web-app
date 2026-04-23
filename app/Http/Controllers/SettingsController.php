<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        return view('dashboard.features.profile_settings', compact('user', 'profile'));
    }
}
