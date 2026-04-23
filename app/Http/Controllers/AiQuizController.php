<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\AIQuiz;
use App\Models\AIQuizAnswer;
use App\Models\User;
use App\Models\Profile;
use App\Services\AIPromptBuilder;
use App\Notifications\CareerEventNotification;
use App\Events\NotificationPushed;


class AIQuizController extends Controller
{
    // ------------------------------------------------------
    // UNIVERSAL GEMINI CALLER
    // ------------------------------------------------------
    private function callGemini($prompt)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); // default also given


        if (!$apiKey) {
            return ['error' => true, 'message' => 'GEMINI_API_KEY missing'];
        }

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json"
            ])->timeout(40)->post($endpoint, [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ]
                ]
            ]);

            if (!$response->ok()) {
                return ['error' => true, 'message' => $response->body()];
            }

            $json = $response->json();

            if (!isset($json["candidates"][0]["content"]["parts"])) {
                return ['error' => true, 'message' => 'Invalid Gemini response'];
            }

            $parts = $json["candidates"][0]["content"]["parts"];

            $finalText = "";
            foreach ($parts as $p) {
                if (isset($p["text"])) $finalText .= $p["text"] . " ";
            }

            return ['error' => false, 'text' => trim($finalText)];

        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    // ------------------------------------------------------
    // START PAGE
    // ------------------------------------------------------
    public function startPage()
    {
       return view('dashboard.features.ai_quiz.questions');

    }

    // ------------------------------------------------------
    // GENERATE QUESTIONS
    // ------------------------------------------------------
    public function generateQuestions()
    {
        $user = Auth::user();
    
        // ⭐ ALLOW RETAKE ⭐
        AIQuiz::where('user_id', $user->id)->delete();
        AIQuizAnswer::where('user_id', $user->id)->delete();
    
        $user->has_taken_ai_quiz = 0;
        $user->save();
    
        if ($user->profile_completed == 0) {
            return redirect('/profile/settings')
                ->with('error', 'Please complete your profile first.');
        }
    
        $profileJson = AIPromptBuilder::build();
    
        $typeInstruction = match($user->user_type) {
            "high_school" => "Ask about favourite subjects, interests, strengths.",
            "student" => "Ask about degree, semester experience, projects, industries.",
            "fresh_graduate" => "Ask about job preference, confidence, goals.",
            "career_switcher" => "Ask about old field, new field, skills transfer.",
            default => "Ask about personality, interests, lifestyle goals."
        };
    
        $prompt = "
    You are an expert AI career counsellor.
    Below is the FULL USER PROFILE in JSON:
    {$profileJson}
    --------------------------------------------
    Generate EXACTLY 8 Career Discovery Questions.
    Return ONLY JSON ARRAY.
    [
      {\"question\": \"...\"},
      {\"question\": \"...\"}
    ]
    No markdown, no explanation.
    --------------------------------------------
    User Type Instructions:
    {$typeInstruction}
    ";
    
        $res = $this->callGemini($prompt);
    
        if ($res['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $res['message']
            ], 500);
        }
    
        $raw = str_replace(["```json", "```"], "", $res["text"]);
        $start = strpos($raw, "[");
        $end   = strrpos($raw, "]");
    
        if ($start === false || $end === false) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid JSON",
                "raw" => $raw
            ], 500);
        }
    
        $jsonString = substr($raw, $start, $end - $start + 1);
        $questions = json_decode($jsonString, true);
    
        if (!is_array($questions)) {
            return response()->json([
                "status" => "error",
                "message" => "JSON decode failed"
            ], 500);
        }
    
        $order = 1;
        foreach ($questions as $q) {
            AIQuiz::create([
                "user_id" => $user->id,
                "career_quiz_id" => 1,
                "question_text" => $q["question"],
                "question_order" => $order++
            ]);
        }
    
        return redirect()->route('ai.quiz.take');
    }
    
    // ------------------------------------------------------
    // FETCH QUESTIONS
    // ------------------------------------------------------
    public function fetchQuestions()
    {
        $user = Auth::user();
    
        $questions = AIQuiz::where('user_id', $user->id)
            ->orderBy('question_order')
            ->get();
    
        return response()->json([
            "status" => "success",
            "already_taken" => $user->has_taken_ai_quiz,
            "questions" => $questions
        ]);
    }

    // ------------------------------------------------------
    // GIBBERISH DETECTOR
    // ------------------------------------------------------
    private function isGibberish($text)
    {
        $clean = preg_replace('/\s+/', '', $text);

        if (strlen($clean) < 3) return true;
        if (preg_match('/(.)\1{3,}/', $clean)) return true;
        if (preg_match('/^[^a-zA-Z]+$/', $clean)) return true;

        $vowels = preg_match_all('/[aeiouAEIOU]/', $clean);
        if ($vowels == 0 && strlen($clean) > 5) return true;

        return false;
    }

    // ------------------------------------------------------
    // SAVE ANSWERS
    // ------------------------------------------------------
    public function saveAnswers(Request $request)
{
    $user = Auth::user();

    if ($user->has_taken_ai_quiz) {
        return response()->json(["status" => "error", "message" => "Already taken"], 403);
    }

    // Get answers from JSON body safely
    $answers = $request->input('answers');

    if (!is_array($answers) || empty($answers)) {
        return response()->json([
            "status" => "error",
            "message" => "Invalid answers format"
        ], 400);
    }

    // Clear previous answers (for safety)
    AIQuizAnswer::where('user_id', $user->id)->delete();

    foreach ($answers as $a) {
        if (empty($a['question_id']) || empty($a['answer'])) {
            continue;
        }

        AIQuizAnswer::create([
            "user_id"     => $user->id,
            "ai_quiz_id"  => $a["question_id"],
            "answer_text" => $a["answer"],
        ]);
    }

    if (AIQuizAnswer::where('user_id', $user->id)->count() == 0) {
        return response()->json([
            "status"  => "error",
            "message" => "Answers did not save. Check your frontend names."
        ], 400);
    }

    $user->has_taken_ai_quiz = 1;
    $user->save();

    $user->notify(new CareerEventNotification(
        "Career Quiz Completed 🎉",
        "Your AI Career Quiz is complete! Your career suggestions are now ready.",
        route('career.suggestions'),
        "bi-question-circle"
    ));

    event(new NotificationPushed(
        $user->fresh()->unreadNotifications()->first(),
        $user->id
    ));

    return response()->json([
        "status"  => "success",
        "message" => "Answers saved successfully!"
    ]);
}


    // ------------------------------------------------------
    // RESULT PAGE
    // ------------------------------------------------------
    public function resultPage()
    {
        return view('dashboard.features.ai_quiz.result');
    }

    // ------------------------------------------------------
    // FINAL CAREER RESULT (FIXED)
    // ------------------------------------------------------
    public function generateFinalResult()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        $answers = AIQuizAnswer::where('user_id', $user->id)->get();
    
        if ($answers->isEmpty()) {
            return response()->json([
                "status" => "error",
                "message" => "No answers found."
            ], 400);
        }
    
        // Validate user answers for gibberish
        $invalidCount = 0;
        foreach ($answers as $a) {
            if ($this->isGibberish($a->answer_text)) {
                $invalidCount++;
            }
        }
    
        // If more than 40% answers are gibberish → block AI
        if ($invalidCount > (count($answers) * 0.4)) {
            return response()->json([
                "status" => "error",
                "message" => "Please provide meaningful answers. Your responses contain too much gibberish."
            ], 422);
        }
    
        // Assemble Q&A text
        $qa = "";
        foreach ($answers as $a) {
            $q = AIQuiz::find($a->ai_quiz_id);
            if ($q) {
                $qa .= "Q: {$q->question_text}\nA: {$a->answer_text}\n\n";
            }
        }
    
        // Strict Prompt
        $prompt = "
    You are an AI Career Analyzer.
    
    Your job is to suggest a career ONLY IF the user answers are meaningful.
    
    STRICT VALIDATION RULES:
    1. If answers are empty, irrelevant, random letters, very short, emojis, or gibberish:
       - DO NOT suggest any career.
       - Reply EXACTLY with:
         'Please provide meaningful answers so I can suggest a suitable career.'
    
    2. Only give a career suggestion if answers show:
       - clear thinking
       - skills/interests
       - personality
       - goals
    
    3. If answers are valid, output ONLY:
    - Top 3 Career Recommendations
    - 1–2 line reasoning each
    - Plain text only (no markdown)
    
    ----------------------------------
    USER PROFILE:
    Education: {$profile->education_level}
    Field: {$profile->field_of_interest}
    Experience: {$profile->experience_years} years
    ----------------------------------
    
    USER ANSWERS:
    {$qa}
    ";
    
        // Call Gemini
        $res = $this->callGemini($prompt);
    
        if ($res["error"]) {
            return response()->json([
                "status" => "error",
                "message" => $res["message"]
            ], 500);
        }
    
        // Clean AI output
        $final = $res["text"] ?? "";
        $final = trim($final);
        $final = preg_replace('/\s+/', ' ', $final);
    
        // Block AI empty / null / invalid text
        if (
            $final === "" ||
            strlen($final) < 10 ||
            strtolower($final) === "null" ||
            strtolower($final) === "undefined" ||
            $final === "{}" ||
            $final === "[]" ||
            stripos($final, "meaningful answers") !== false // Model returned our warning message
        ) {
            return response()->json([
                "status" => "error",
                "message" => "AI returned an invalid or empty career suggestion. Please try again with clearer answers."
            ], 500);
        }
    
        // Save ONLY valid suggestions
        $user->career_suggestion = $final;
        $user->save();
    
        return response()->json([
            "status" => "success",
            "career_suggestion" => $final
        ]);
    }
    
}
