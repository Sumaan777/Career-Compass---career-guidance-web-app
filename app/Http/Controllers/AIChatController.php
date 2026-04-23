<?php

namespace App\Http\Controllers;

use App\Models\AIChat;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AIChatController extends Controller
{
    // Show chat page
    public function index()
    {
        $user = Auth::user();

        $messages = AIChat::where('user_id', $user->id)
            ->orderBy('created_at')
            ->take(50)
            ->get();

        return view('dashboard.features.ai_chat', compact('messages', 'user'));
    }

    // Handle AI response (called by AJAX)
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $user = Auth::user();
        $msg  = trim($request->message);

        // Save user message
        AIChat::create([
            'user_id' => $user->id,
            'role'    => 'user',
            'message' => $msg,
        ]);

        // Last few messages for context
        $history = AIChat::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(15)
            ->get()
            ->sortBy('created_at')
            ->map(function ($item) {
                return [
                    'role'    => $item->role === 'assistant' ? 'assistant' : 'user',
                    'content' => $item->message,
                ];
            })
            ->values()
            ->all();

        // Profile context
        $profile = Profile::where('user_id', $user->id)->first();

        $profileContext = $profile ? "
User Profile:
- Full name: {$profile->full_name}
- Education level: {$profile->education_level}
- Field of interest: {$profile->field_of_interest}
- Skills: {$profile->skills}
- Experience years: {$profile->experience_years}
- Location: {$profile->location}
" : "User profile is incomplete. Ask relevant questions to better understand the user.";

        // System prompt
        $systemPrompt = "
You are 'CareerCompass AI Mentor', an expert, friendly, and practical career counselor.

GOALS:
- Give personalized career guidance using the user's profile, interests, and skills.
- Suggest realistic job roles, skills to learn, courses, and certifications.
- Help with resume tips, interview prep, and clear roadmaps.
- Ask follow-up questions if information is missing.
- Keep answers structured, clear, and not too long.
- Don't hallucinate. If unsure, say so.

Here is the user's current context:
{$profileContext}
";

        // Build messages array
        $messagesForModel   = [];
        $messagesForModel[] = ['role' => 'system', 'content' => $systemPrompt];
        foreach ($history as $h) {
            $messagesForModel[] = $h;
        }
        // Latest user msg is already in history because we saved it above

        $model = env('GROQ_MODEL', 'openai/gpt-oss-20b');

        try {
            // GROQ example (you already use it in roadmap feature)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => $model,
                'messages'    => $messagesForModel,
                'max_tokens'  => 500,
                'temperature' => 0.8,
            ]);

            if ($response->failed()) {
                throw new \Exception('AI API request failed');
            }

            $data  = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? null;

            if (!$reply) {
                throw new \Exception('Empty AI reply');
            }
        } catch (\Throwable $e) {
            $reply = "I'm having some technical issues right now 😅. Please try again in a moment.";
        }

        // Save AI reply
        AIChat::create([
            'user_id' => $user->id,
            'role'    => 'user',
            'message' => $reply,
        ]);

        return response()->json([
            'status' => 'ok',
            'reply'  => $reply,
        ]);
    }
    public function demo(Request $request)
{
    $request->validate([
        'message' => 'required|string'
    ]);

    $msg = trim($request->message);
    $model = env('GROQ_MODEL', 'openai/gpt-oss-20b');

    // Short, safe system prompt for guest demo
    $systemPrompt = "
You are 'CareerCompass AI Preview', an AI career mentor.

GOALS:
- Answer briefly in 3–5 lines.
- Give realistic, practical suggestions.
- Don't assume detailed profile; user is a guest.
- If user asks for very long plan, summarize instead.
";

    $messagesForModel = [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => $msg],
    ];

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY_SECONDARY'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'       => $model,
            'messages'    => $messagesForModel,
            'max_tokens'  => 300,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            throw new \Exception('AI API request failed');
        }

        $data  = $response->json();
        $reply = $data['choices'][0]['message']['content'] ?? null;

        if (!$reply) {
            throw new \Exception('Empty AI reply');
        }

    } catch (\Throwable $e) {
        $reply = "I'm having trouble generating a preview right now. Please try again in a few moments.";
    }

    return response()->json([
        'reply' => $reply,
    ]);
}

}
