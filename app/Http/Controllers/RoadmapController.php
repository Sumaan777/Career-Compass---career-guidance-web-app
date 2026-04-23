<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Roadmap;
use App\Models\Profile;
use App\Models\Career;

class RoadmapController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Roadmap Page
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = Auth::user();
    
        // Latest roadmap
        $lastRoadmap = Roadmap::where('user_id', $user->id)
            ->latest()
            ->first();
    
        $roadmapData = $lastRoadmap && $lastRoadmap->roadmap_json
            ? json_decode($lastRoadmap->roadmap_json, true)
            : null;
    
        // 🔥 STEP 5.1 — FETCH TREND FOR ROADMAP'S CAREER
        $trendForRoadmap = null;
    
        if ($lastRoadmap && $lastRoadmap->career) {
    
            $trendForRoadmap = \App\Models\CareerTrend::where('career_name', $lastRoadmap->career)
                ->where('region', 'Pakistan')
                ->latest()
                ->first();
        }
    
// STEP 2 — Generate Mermaid Code (ONLY if roadmap exists)
$mermaidCode = null;

if ($roadmapData) {
    $mermaidCode = $this->roadmapToMermaid($roadmapData);
}

return view('dashboard.features.roadmap', [
    'lastRoadmap'      => $lastRoadmap,
    'roadmapData'      => $roadmapData,
    'mermaidCode'      => $mermaidCode, // 👈 VERY IMPORTANT
    'user'             => $user,
    'trendForRoadmap'  => $trendForRoadmap,
]);

    }
    

    /*
    |--------------------------------------------------------------------------
    | Career Search for Autocomplete
    |--------------------------------------------------------------------------
    */
    public function searchCareers(Request $request)
    {
        $q = $request->input('q');

        $careers = Career::where('title', 'LIKE', "%{$q}%")
            ->orWhere('category', 'LIKE', "%{$q}%")
            ->limit(15)
            ->get(['id', 'title', 'slug', 'category', 'skills_tags']);

        return response()->json($careers);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Full AI Roadmap (Gemini 1.5 Flash)
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request)
    {
        $request->validate([
            'career_query' => 'required|string|min:2',
        ]);
    
        $user = Auth::user();
        $careerInput = trim($request->career_query);
    
        /*
        |--------------------------------------------------------------------------
        | Step 1: Check if career exists in DB
        |--------------------------------------------------------------------------
        */
        $careerModel = Career::where('title', $careerInput)->first();
    
        if (!$careerModel) {
            $careerModel = Career::create([
                'title' => $careerInput,
                'slug'  => Str::slug($careerInput),
                'category' => null,
                'skills_tags' => [],
            ]);
        }
    
        /*
        |--------------------------------------------------------------------------
        | Step 2: Add user profile context
        |--------------------------------------------------------------------------
        */
        $profile = Profile::where('user_id', $user->id)->first();
    
        $profileText = $profile ? "
    - Education level: {$profile->education_level}
    - Field of interest: {$profile->field_of_interest}
    - Skills: {$profile->skills}
    - Experience years: {$profile->experience_years}
    - Location: {$profile->location}
    " : "No strong profile data available.";
    
        /*
        |--------------------------------------------------------------------------
        | Step 3: Build the BEST Prompt for Roadmap Generation
        |--------------------------------------------------------------------------
        */
        $prompt = "
    You are a senior career architect. Generate a complete step-by-step roadmap.
    
    Career or goal:
    \"{$careerInput}\"
    
    User background:
    {$profileText}
    
    Return ONLY valid JSON in this exact structure:
    
    {
      \"career_title\": \"...\",
      \"short_summary\": \"...\",
      \"global_skills\": [\"...\"],
      \"prerequisites\": [\"...\"],
      \"phases\": [
        {
          \"name\": \"...\",
          \"level\": \"Beginner\" | \"Intermediate\" | \"Advanced\",
          \"duration\": \"6-12 months\",
          \"goals\": [\"...\"],
          \"skills_to_learn\": [\"...\"],
          \"actions\": [\"...\"],
          \"resources\": [\"...\"] 
        }
      ],
      \"suggested_roles\": [\"...\"]  
    }
    
    Rules:
    - JSON only, no markdown.
    - Keep roadmap realistic for Pakistan or similar regions.
    - Avoid naming paid platforms.
    - Focus on practical skills, real actions, and learning steps.
    ";
    
        /*
        |--------------------------------------------------------------------------
        | Step 4: Call GROQ API Here
        |--------------------------------------------------------------------------
        */
        $apiKey = env('GROQ_API_KEY');
        $model = env('GROQ_MODEL', 'openai/gpt-oss-20b');

    
        if (!$apiKey) {
            return ['error' => true, 'message' => 'GROQ_API_KEY missing'];
        }
    
        $endpoint = "https://api.groq.com/openai/v1/chat/completions";
    
        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => "Bearer {$apiKey}"
            ])
            ->timeout(40)
            ->post($endpoint, [
                "model" => $model,
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "Return only valid JSON. No markdown, no explanations."
                    ],
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ],
                "temperature" => 0.2
            ]);
    
            if (!$response->successful()) {
                return back()->with('error', 'AI request failed. Try again.');
            }
    
            $data = $response->json();
    
            // Extract the text returned by GROQ
            $jsonText = $data['choices'][0]['message']['content'] ?? null;
    
            if (!$jsonText) {
                return back()->with('error', 'AI returned empty response.');
            }
    
            // Clean JSON
            $clean = trim($jsonText);
            $clean = preg_replace('/^```json|```$/i', '', $clean);
    
            $roadmapArray = json_decode($clean, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'JSON parse error from AI.');
            }
    
            /*
            |--------------------------------------------------------------------------
            | Step 5: Save Roadmap to DB
            |--------------------------------------------------------------------------
            */
            Roadmap::create([
                'user_id'      => $user->id,
                'career'       => $roadmapArray['career_title'] ?? $careerInput,
                'roadmap_json' => json_encode($roadmapArray),
            ]);
    
            /*
            |--------------------------------------------------------------------------
            | Step 6: Auto-skill learning: update careers DB
            |--------------------------------------------------------------------------
            */
            $skills = $roadmapArray['global_skills'] ?? [];
    
            if (!empty($skills)) {
                $careerModel->skills_tags = $skills;
                $careerModel->save();
            }
    
            /*
            |--------------------------------------------------------------------------
            | Step 7: Done. Redirect to Roadmap Page
            |--------------------------------------------------------------------------
            */
            return redirect()
                ->route('career.roadmap')
                ->with('success', 'Roadmap generated successfully!');
    
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    

    private function roadmapToMermaid(array $roadmap): string
{
    $mermaid = "timeline\n";
    $mermaid .= "title {$roadmap['career_title']} Roadmap\n";

    foreach ($roadmap['phases'] as $phase) {
        $mermaid .= "{$phase['duration']} : {$phase['name']} ({$phase['level']})\n";

        foreach ($phase['goals'] as $goal) {
            $mermaid .= "  : {$goal}\n";
        }
    }

    return $mermaid;
}

}
