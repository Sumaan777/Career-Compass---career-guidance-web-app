<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Skill;
use App\Models\SkillGapAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SkillGapController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. Show Skill Gap Analyzer Page
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = Auth::user();

        $latestAnalysis = SkillGapAnalysis::where('user_id', $user->id)
            ->latest()
            ->first();

        $profile = Profile::with('skills')->where('user_id', $user->id)->first();

        $skills = $profile ? $profile->skills : collect([]);

        return view('dashboard.features.skill_gap', compact('latestAnalysis', 'profile', 'skills'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Run Skill Gap Analysis
    |--------------------------------------------------------------------------
    */
    public function analyze(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'target_career' => 'nullable|string|max:255',
    ]);

    // 1) Determine target career
    $targetCareer = $request->target_career ?: $user->career_suggestion;

    if (!$targetCareer) {
        return back()->with('error', 'Please enter a target career or complete Career Suggestions first.');
    }

    // 2) Get skills from profile.skills table
    $profile = Profile::with('skills')->where('user_id', $user->id)->first();

    if (!$profile || $profile->skills->count() == 0) {
        return back()->with('error', 'Please add your skills in your profile first.');
    }

    // Convert skill objects → clean lowercase strings
    $currentSkillsArray = [];
    foreach ($profile->skills as $skill) {
        $name = strtolower(trim($skill->skill_name));
        if ($name !== '') {
            $currentSkillsArray[] = $name;
        }
    }
    $currentSkillsArray = array_values(array_unique($currentSkillsArray));

    if (empty($currentSkillsArray)) {
        return back()->with('error', 'No valid skills found in your profile.');
    }

    // 3) Call AI to get required skills list
    try {
        $aiResponse = $this->fetchRequiredSkillsFromAI($targetCareer, $currentSkillsArray);
    } catch (\Exception $e) {
        return back()->with('error', 'AI error (required skills): ' . $e->getMessage());
    }

    $requiredSkills = array_map('strtolower', $aiResponse['required_skills'] ?? []);
    $recommendedSkills = array_map('strtolower', $aiResponse['recommended_skills'] ?? []);

    if (empty($requiredSkills)) {
        return back()->with('error', 'No required skills returned by AI.');
    }

    // 4) SEMANTIC MATCHING via AI
    try {
        $semantic = $this->fetchSemanticSkillMatchFromAI(
            $targetCareer,
            $currentSkillsArray,
            $requiredSkills
        );

        $matched = array_map('strtolower', $semantic['matched_skills'] ?? []);
        $missing = array_map('strtolower', $semantic['missing_skills'] ?? []);
        $extra   = array_map('strtolower', $semantic['extra_skills'] ?? []);

    } catch (\Exception $e) {
        // Fallback: simple comparison if semantic AI fails
        $matched = array_values(array_intersect($currentSkillsArray, $requiredSkills));
        $missing = array_values(array_diff($requiredSkills, $currentSkillsArray));
        $extra   = array_values(array_diff($currentSkillsArray, $requiredSkills));
    }

    // 5) Save analysis
    SkillGapAnalysis::create([
        'user_id'            => $user->id,
        'target_career'      => $targetCareer,
        'current_skills'     => $currentSkillsArray,
        'required_skills'    => $requiredSkills,
        'missing_skills'     => $missing,
        'matched_skills'     => $matched,
        'extra_skills'       => $extra,
        'recommended_skills' => $recommendedSkills,
    ]);

    return redirect()
        ->route('skill-gap.index')
        ->with('success', 'Skill gap analysis generated successfully with semantic AI matching.');
}

    /*
    |--------------------------------------------------------------------------
    | 3. AI Skill Extraction Method
    |--------------------------------------------------------------------------
    */
    private function fetchRequiredSkillsFromAI(string $career, array $currentSkills): array
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); // default also given

        if (!$apiKey) {
            throw new \Exception('Gemini API key missing in .env');
        }

        $prompt = "
You are a career skill analysis expert.

Target career: \"$career\"  
User's current skills: \"" . implode(', ', $currentSkills) . "\"

Return a clean JSON ONLY:
{
  \"required_skills\": [\"skill1\", \"skill2\", \"skill3\"],
  \"recommended_skills\": [\"skillA\", \"skillB\"]
}
No explanation.
";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=$apiKey",
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]
        );

        if (!$response->successful()) {
            throw new \Exception('Gemini API error: ' . $response->body());
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Clean markdown
        $text = preg_replace('/```json|```/i', '', trim($text));

        $json = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON returned by AI.");
        }

        return $json;
    }
    private function fetchSemanticSkillMatchFromAI(
        string $career,
        array $userSkills,
        array $requiredSkills
    ): array {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); // default also given

    
        if (!$apiKey) {
            throw new \Exception('Gemini API key missing in .env');
        }
    
        $prompt = "
    You are an expert career and skills analysis engine.
    
    Task:
    Given:
    1) A target career
    2) A list of the user's current skills
    3) A list of required skills for that career
    
    You must semantically compare the skills, considering:
    - Synonyms
    - Frameworks vs languages (e.g., Laravel ≈ PHP backend framework)
    - Libraries vs core skills
    - Related concepts (e.g., HTML/CSS ≈ front-end basics)
    
    Strictly classify skills into:
    - matched_skills: skills the user already has or very closely related (synonyms / frameworks / same domain)
    - missing_skills: important required skills the user is clearly lacking
    - extra_skills: user skills that are not required but could still be useful
    
    IMPORTANT:
    - Think semantically. 
    - DO NOT just match exact strings.
    
    Return ONLY valid JSON:
    
    {
      \"matched_skills\": [\"skill1\", \"skill2\"],
      \"missing_skills\": [\"skill3\", \"skill4\"],
      \"extra_skills\": [\"skill5\"]
    }
    
    Target career: \"$career\"
    User skills: " . implode(', ', $userSkills) . "
    Required skills: " . implode(', ', $requiredSkills) . "
    ";
    
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=$apiKey",
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]
        );
    
        if (!$response->successful()) {
            throw new \Exception('Gemini semantic match API error: ' . $response->body());
        }
    
        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    
        $text = preg_replace('/```json|```/i', '', trim($text));
    
        $json = json_decode($text, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON from semantic AI: ' . json_last_error_msg());
        }
    
        return $json;
    }
    
}
