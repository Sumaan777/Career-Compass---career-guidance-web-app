<?php

namespace App\Http\Controllers;

use App\Models\JobRecommendation;
use App\Models\JobSearch;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class JobRecommenderController extends Controller
{
    // -------------------------------------------------------------
    // 1. SHOW JOB RECOMMENDER PAGE
    // -------------------------------------------------------------
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        $locations = ['Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Remote'];

        $recentSearches = JobSearch::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $topLocations = JobRecommendation::where('user_id', $user->id)
            ->selectRaw('location, COUNT(*) as total')
            ->groupBy('location')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $topRoles = JobRecommendation::where('user_id', $user->id)
            ->selectRaw('ai_job_title, COUNT(*) as total')
            ->groupBy('ai_job_title')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('dashboard.features.job_recommender', compact(
            'user',
            'profile',
            'locations',
            'recentSearches',
            'topLocations',
            'topRoles'
        ));
    }



    // -------------------------------------------------------------
    // 2. GENERATE JOB RECOMMENDATIONS (AI + JOB API)
    // -------------------------------------------------------------
    public function recommend(Request $request)
    {
        try {
            // -----------------------------
            // VALIDATE INPUT + LOAD PROFILE
            // -----------------------------
            $request->validate([
                'location' => 'required|string|max:255',
            ]);

            $user = Auth::user();
            $profile = Profile::where('user_id', $user->id)->firstOrFail();

            $location = $request->location;
            $degree   = $profile->education_level ?? 'Not specified';
            $skills   = $profile->skills ?? 'Not specified';
            $field    = $profile->field_of_interest ?? 'Not specified';
            $expYears = $profile->experience_years ?? '0';



            // -----------------------------
            // STRICT JSON PROMPT (NO SCHEMA)
            // -----------------------------
            $prompt = "
You are an AI job recommender.  
Your output MUST be valid JSON only.  
NO explanations, NO extra text, NO markdown.

USER DATA:
Degree: {$degree}
Skills: {$skills}
Field: {$field}
Experience: {$expYears}
Location: {$location}

RULES:
- Output ONLY JSON.
- No text before or after JSON.
- Maximum 5 jobs.
- Each job must contain 'title' and 'reason' (1 short sentence).

FORMAT:
{
  \"jobs\": [
    {\"title\": \"Backend Developer\", \"reason\": \"Short reason here.\"}
  ]
}
";



            // -----------------------------
            // GEMINI API CALL (WORKING)
            // -----------------------------
            $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); // default also given


            if (!$apiKey) {
                return response()->json([
                    "status"  => "error",
                    "message" => "GEMINI_API_KEY missing in .env"
                ]);
            }

            $endpoint =
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $geminiResponse = Http::withHeaders([
                "Content-Type" => "application/json"
            ])->timeout(40)->post($endpoint, [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "temperature"        => 0.2,
                    "topP"               => 0.9,
                    "maxOutputTokens"    => 1500,
                    "response_mime_type" => "application/json"
                ]
            ]);

            $body = $geminiResponse->json();



            // -----------------------------
            // CHECK GEMINI ERRORS
            // -----------------------------
            if (isset($body['error'])) {
                return response()->json([
                    "status"  => "error",
                    "message" => "Gemini error: " . ($body['error']['message'] ?? 'Unknown'),
                    "raw"     => $body
                ]);
            }



            // -----------------------------
            // EXTRACT JSON FROM GEMINI
            // -----------------------------
            $content = data_get($body, 'candidates.0.content.parts.0.text');

            if (!$content) {
                return response()->json([
                    "status" => "error",
                    "message" => "Gemini returned empty content.",
                    "raw" => $body
                ]);
            }

            $start = strpos($content, "{");
            if ($start !== false) {
                $content = substr($content, $start);
            }

            $aiData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($aiData['jobs'])) {
                return response()->json([
                    "status"  => "error",
                    "message" => "Invalid JSON returned by Gemini.",
                    "raw"     => $content
                ]);
            }

            $aiJobs = $aiData['jobs'];



            // -----------------------------
            // SAVE SEARCH SNAPSHOT
            // -----------------------------
            $search = JobSearch::create([
                'user_id'          => $user->id,
                'degree'           => $degree,
                'skills'           => $skills,
                'field_of_interest'=> $field,
                'experience_years' => $expYears,
                'location'         => $location,
                'total_results'    => 0,
                'ai_jobs'          => $aiJobs
            ]);



            // --------------------------------------------------------
            // 3. USE JSEARCH (RAPIDAPI) INSTEAD OF ADZUNA (PAKISTAN SUPPORTED)
            // --------------------------------------------------------
            $finalResults = [];
            $count = 0;

            foreach ($aiJobs as $index => $jobAi) {

                $aiTitle = $jobAi['title'];
                $reason  = $jobAi['reason'];

                try {
                    $response = Http::withHeaders([
                        "X-RapidAPI-Key" => env("RAPIDAPI_KEY"),
                        "X-RapidAPI-Host" => "jsearch.p.rapidapi.com"
                    ])->get("https://jsearch.p.rapidapi.com/search", [
                        "query" => "{$aiTitle} in {$location}",
                        "page" => 1,
                        "num_pages" => 1,
                    ]);

                    $results = $response->json('data') ?? [];

                } catch (\Exception $e) {
                    $results = [];
                }

                $cards = [];

                foreach ($results as $item) {

                    $count++;

                    $rec = JobRecommendation::create([
                        'user_id'          => $user->id,
                        'degree'           => $degree,
                        'skills'           => $skills,
                        'field_of_interest'=> $field,
                        'experience_years' => $expYears,
                        'location'         => $location,
                        'ai_job_title'     => $aiTitle,
                        'reason'           => $reason,
                        'source'           => 'jsearch',
                        'job_title'        => $item['job_title'] ?? null,
                        'company'          => $item['employer_name'] ?? null,
                        'job_location'     => $item['job_city'] ?? null,
                        'redirect_url'     => $item['job_apply_link'] ?? null,
                        'salary'           => $item['job_min_salary'] ?? null,
                        'posted_at'        => $item['job_posted_at_datetime_utc'] ?? null,
                        'match_score'      => 100 - ($index * 10),
                        'raw_api'          => $item,
                    ]);

                    $cards[] = [
                        "id"            => $rec->id,
                        "job_title"     => $rec->job_title,
                        "company"       => $rec->company,
                        "job_location"  => $rec->job_location,
                        "redirect_url"  => $rec->redirect_url,
                        "salary"        => $rec->salary,
                        "posted_at"     => optional($rec->posted_at)->toDateString(),
                        "match_score"   => $rec->match_score,
                    ];
                }

                $finalResults[] = [
                    "ai_job_title" => $aiTitle,
                    "reason"       => $reason,
                    "listings"     => $cards,
                ];
            }

            $search->update(['total_results' => $count]);



            // -----------------------------
            // FINAL RESPONSE
            // -----------------------------
            return response()->json([
                "status" => "success",
                "data"   => $finalResults
            ]);



        } catch (\Throwable $e) {
            return response()->json([
                "status"  => "fatal",
                "message" => $e->getMessage(),
                "file"    => $e->getFile(),
                "line"    => $e->getLine()
            ]);
        }
    }



    // -------------------------------------------------------------
    // 3. HISTORY PAGE
    // -------------------------------------------------------------
    public function history()
    {
        $user = Auth::user();

        $recommendations = JobRecommendation::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('dashboard.features.job_history', compact('recommendations'));
    }
}
