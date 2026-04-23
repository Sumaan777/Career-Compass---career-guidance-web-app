<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LearningResourceController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    |  Clean & Extract JSON from AI Response
    |--------------------------------------------------------------------------
    |  - Removes ```json and ``` blocks
    |  - Extracts only the JSON array portion
    |  - Fixes invalid JSON coming from Gemini
    */
    private function extractJson(string $text)
    {
        // Remove Markdown code fences
        $text = preg_replace('/```json|```/i', '', $text);

        // Find first '[' and last ']'
        $start = strpos($text, '[');
        $end   = strrpos($text, ']');

        if ($start === false || $end === false) {
            return null;
        }

        // Extract substring containing JSON array
        $jsonString = substr($text, $start, $end - $start + 1);

        return trim($jsonString);
    }



    /*
    |--------------------------------------------------------------------------
    | 1. Show Learning Resources Page
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $resources = LearningResource::where('user_id', Auth::id())->get();

        return view('dashboard.features.learning_resources', compact('resources'));
    }



    /*
    |--------------------------------------------------------------------------
    | 2. Generate Learning Resources via AI
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request)
    {
        $request->validate([
            'skills' => 'nullable|array',
            'skills.*' => 'string',
            'manual_skills' => 'nullable|string'
        ]);
        

        $skillGaps = $request->skills;
        $user = Auth::user();
        // -------- DETERMINE SKILLS SOURCE --------
if ($request->has('skills')) {
    // Coming from Skill Gap page
    $skillGaps = $request->skills;

} elseif ($request->filled('manual_skills')) {
    // Coming from manual search
    $skillGaps = array_map(
        'trim',
        explode(',', $request->manual_skills)
    );

} else {
    return back()->with('error', 'Please provide skills.');
}


        // Remove previously stored recommendations
        LearningResource::where('user_id', $user->id)->delete();


        /*
        |--------------------------------------------------------------------------
        | AI PROMPT
        |--------------------------------------------------------------------------
        */
        $prompt = "
You are a professional AI learning resource recommender.

For each of these skills: " . implode(', ', $skillGaps) . "

Generate EXACT JSON ONLY in this format:

[
  {
    \"skill\": \"Laravel\",
    \"title\": \"Laravel Bootcamp\",
    \"platform\": \"Laracasts\",
    \"url\": \"https://laracasts.com\",
    \"description\": \"A beginner-friendly course for Laravel basics.\",
    \"difficulty\": \"Beginner\",
    \"duration\": 10
  }
]

IMPORTANT:
- Return ONLY pure JSON.
- No explanation.
- No text before or after JSON.
";


        /*
        |--------------------------------------------------------------------------
        | CALL GEMINI API
        |--------------------------------------------------------------------------
        */
        try {

            $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); // default also given


            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "x-goog-api-key" => $apiKey,
            ])->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
                [
                    "contents" => [
                        ["parts" => [["text" => $prompt]]]
                    ]
                ]
            );

            $aiRaw = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$aiRaw) {
                return back()->with('error', 'AI response is empty or invalid.');
            }



            /*
            |--------------------------------------------------------------------------
            | CLEAN + PARSE JSON
            |--------------------------------------------------------------------------
            */
            $cleanJson = $this->extractJson($aiRaw);

            if (!$cleanJson) {
                return back()->with('error', 'AI did not return JSON.');
            }

            $resources = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Invalid JSON: ' . json_last_error_msg());
            }

            if (!is_array($resources)) {
                return back()->with('error', 'AI JSON format incorrect.');
            }



            /*
            |--------------------------------------------------------------------------
            | SAVE RESOURCES TO DATABASE
            |--------------------------------------------------------------------------
            */
            foreach ($resources as $item) {

                LearningResource::create([
                    'user_id'     => $user->id,
                    'skill'       => $item['skill'] ?? null,
                    'title'       => $item['title'] ?? null,
                    'platform'    => $item['platform'] ?? null,
                    'url'         => $item['url'] ?? null,
                    'description' => $item['description'] ?? null,
                    'difficulty'  => $item['difficulty'] ?? null,
                    'duration'    => $item['duration'] ?? null,
                ]);
            }



            return redirect()
                ->route('learning.resources')
                ->with('success', 'Learning resources generated successfully!');


        } catch (\Exception $e) {

            return back()->with(
                'error',
                "AI API Error: " . $e->getMessage()
            );
        }
    }
}
