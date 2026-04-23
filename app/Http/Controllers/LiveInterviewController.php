<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterviewSession;
use App\Models\InterviewMessage;
use App\Models\Resume;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LiveInterviewController extends Controller
{
    // Load single-page UI
    public function singlePage()
    {
        return view('dashboard.features.interview');
    }

    // Start interview
    public function start(Request $request)
    {
        $request->validate([
            'field' => 'required|string|max:255'
        ]);

        $userId = Auth::id();

        // Create new interview session
        $session = InterviewSession::create([
            'user_id' => $userId,
            'field'   => $request->field,
        ]);

        return response()->json([
            'status' => 'success',
            'session_id' => $session->id
        ]);
    }

    // Chat handler
    public function chat(Request $request, InterviewSession $session)
    {
        $request->validate([
            'answer' => 'nullable|string'
        ]);

        $user = Auth::user();
        $profile = $user->profile ?? null;

        // User resume analysis
        $resume = Resume::where('user_id', $user->id)->first();
        $analysis = $resume->analysis_text ?? "No resume analysis available.";

        // Profile data
        $profileText = $profile ? "
        Name: {$profile->full_name}
        Education: {$profile->education_level}
        Skills: {$profile->skills}
        Experience: {$profile->experience_years}
        Field of Interest: {$profile->field_of_interest}
        Location: {$profile->location}
        Bio: {$profile->bio}
        " : "Profile data missing.";

        // Save user's answer
        if ($request->answer) {
            InterviewMessage::create([
                'session_id' => $session->id,
                'role' => 'user',
                'message' => $request->answer,
            ]);
        }

        // Build conversation history
        $history = $session->messages()->orderBy('created_at')->get()->map(function ($m) {
            return [
                'role' => $m->role,
                'message' => $m->message
            ];
        })->toArray();

        // AI prompt
        $field = $session->field;

        $prompt = "
You are a friendly job interviewer for the position: {$field}

USER PROFILE:
{$profileText}

RESUME ANALYSIS:
{$analysis}

INTERVIEW RULES:
- Ask one question at a time  
- Give feedback after each answer  
- Score the answer (0–10)  
- Stop after 5–7 questions  
- At the end, provide a final report  

CHAT HISTORY UNTIL NOW:
" . json_encode($history) . "

Return JSON ONLY like this:
{
  \"question\": \"...\",
  \"feedback\": \"...\",
  \"score_delta\": 6,
  \"finished\": false,
  \"final_report\": \"...\"
}
";

        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); // default also given


        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=$apiKey";

        $response = Http::post($url, [
            "contents" => [[
                "parts" => [[ "text" => $prompt ]]
            ]]
        ]);

        
        $json = $response->json();

        $raw = "";
        if (isset($json["candidates"][0]["content"]["parts"])) {
            foreach ($json["candidates"][0]["content"]["parts"] as $p) {
                if (isset($p["text"])) $raw .= $p["text"];
            }
        }

        preg_match('/\{(?:[^{}]|(?R))*\}/', $raw, $match);
        $data = json_decode($match[0] ?? "{}", true);

        $question = $data['question'] ?? "Could not generate question.";
        $feedback = $data['feedback'] ?? "";
        $scoreDelta = intval($data['score_delta'] ?? 0);
        $finished = $data['finished'] ?? false;
        $finalReport = $data['final_report'] ?? "";

        // Save AI question
        InterviewMessage::create([
            'session_id' => $session->id,
            'role' => 'ai',
            'message' => $question,
            'score' => $scoreDelta
        ]);

        // Update session progress
        $session->question_count += 1;
        $session->total_score += $scoreDelta;

        if ($finished) {
            $session->status = 'finished';
            $session->final_report = $finalReport;
        }

        $session->save();

        return response()->json([
            'question' => $question,
            'feedback' => $feedback,
            'total_score' => $session->total_score,
            'finished' => $finished,
            'final_report' => $finalReport
        ]);
    }
}
