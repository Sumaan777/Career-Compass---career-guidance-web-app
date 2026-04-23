<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\AcademicPathResult;
use App\Models\Profile;

class AcademicPathController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. Show Academic Path Validator Page
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $latest = AcademicPathResult::where('user_id', Auth::id())
                    ->latest()
                    ->first();

        return view('dashboard.features.academic_path', compact('latest'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Validate Academic Path via GROQ
    |--------------------------------------------------------------------------
    */
    public function validatePath(Request $request)
    {
        $request->validate([
            'target_career' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            return back()->with('error', 'Please complete your profile first.');
        }

        $target = $request->target_career;

        /*
        |--------------------------------------------------------------------------
        | GROQ PROMPT
        |--------------------------------------------------------------------------
        */
        $prompt = "
You are an expert academic counselor.

Analyze whether this user is academically eligible for the target career.

USER ACADEMIC INFORMATION:
- Education Level: {$profile->education_level}
- Field of Study: {$profile->field_of_interest}
- Skills: {$profile->skills}
- Experience Years: {$profile->experience_years}

TARGET CAREER: {$target}

Return ONLY JSON in this exact structure:

{
  \"education_match\": {
      \"eligible\": true/false,
      \"reasons\": [\"...\", \"...\"] 
  },
  \"required_degrees\": [\"Bachelor in Computer Science\", \"Bootcamp\", \"Diploma\"],
  \"recommended_paths\": [
      {
        \"path\": \"Traditional University Route\",
        \"steps\": [\"Do this\", \"Do that\"]
      }
  ],
  \"certifications\": [\"Google IT Support\", \"AWS Cloud Practitioner\"],
  \"summary\": \"Short explanation...\"
}

NO extra text.
";

        /*
        |--------------------------------------------------------------------------
        | CALL GROQ API
        |--------------------------------------------------------------------------
        */
        try {
            $apiKey = env('GROQ_API_KEY');
            $model = env('GROQ_MODEL', 'openai/gpt-oss-20b');
            

            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => "Bearer {$apiKey}",
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                "model" =>$model,
                "messages" => [
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0.3
            ]);

            $data = $response->json();

            $aiText = $data['choices'][0]['message']['content'] ?? null;

            if (!$aiText) {
                return back()->with('error', 'Invalid response from GROQ.');
            }

            // Clean JSON
            $clean = preg_replace('/```json|```/i', '', $aiText);

            $json = json_decode($clean, true);

            if (!$json) {
                return back()->with('error', 'GROQ returned invalid JSON.');
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE TO DATABASE
            |--------------------------------------------------------------------------
            */
            AcademicPathResult::create([
                'user_id' => $user->id,
                'target_career' => $target,
                'education_match' => $json['education_match'] ?? [],
                'required_degrees' => $json['required_degrees'] ?? [],
                'recommended_paths' => $json['recommended_paths'] ?? [],
                'certifications' => $json['certifications'] ?? [],
                'summary' => $json['summary'] ?? null,
            ]);

            return redirect()->route('academic.path')
                   ->with('success', 'Academic path validated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'GROQ API Error: ' . $e->getMessage());
        }
    }
}
