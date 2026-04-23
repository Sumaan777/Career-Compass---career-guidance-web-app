<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

// Add these for notifications
use App\Notifications\CareerEventNotification;
use App\Events\NotificationPushed;

class CareerSuggestionController extends Controller
{
    public function show()
{
    $user = Auth::user();

    // If suggestion is empty → generate one
    if (!$user->career_suggestion || strlen($user->career_suggestion) < 5) {

        // Call AIQuizController final result generator
        $quiz = new \App\Http\Controllers\AIQuizController();
        $response = $quiz->generateFinalResult();

        // If error from AI → pass clean error message to view
        if (isset($response['status']) && $response['status'] === 'error') {
            return view('dashboard.features.suggestions', [
                'suggestions' => null,
                'error' => $response['message']
            ]);
            
        }

        // Otherwise assign new suggestion
        $user->career_suggestion = $response['career_suggestion'];
        $user->save();
    }

    return view('dashboard.features.suggestions', [
        'suggestions' => $user->career_suggestion   // <-- blade wants THIS name
    ]);
    
}

}
