<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Career;
use App\Models\CareerTrend;
use Carbon\Carbon;
use App\Models\CareerTrendSnapshot;


class CareerTrendController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show trend insight for a given career
    |--------------------------------------------------------------------------
    */
    public function show(Request $request)
    {
        $user = Auth::user();
        

        // User input or fallback
        $careerName = $request->query('career');

        
        if (!$careerName) {
            $lastCareer = \App\Models\Roadmap::where('user_id', $user->id)->latest()->value('career');
            $careerName = $lastCareer ?: 'AI Engineer';
        }

        $region = 'Pakistan';

        // Optional link with careers table
        $careerModel = Career::where('title', $careerName)->first();

        // Check old trend (7 days expiration)
        $trend = CareerTrend::where('career_name', $careerName)
            ->where('region', $region)
            ->latest()
            ->first();

        $isStale = true;

        if ($trend) {
            $isStale = $trend->updated_at->lt(now()->subDays(7));
        }

        // Refresh if no record or stale
        if (!$trend || $isStale) {
            $trend = $this->refreshTrendFromAI($careerName, $region, $careerModel);
        }

        $fromDate = now()->subMonths(6)->toDateString();

$history = CareerTrendSnapshot::where('career_name', $careerName)
    ->where('region', $region)
    ->where('snapshot_date', '>=', $fromDate)
    ->orderBy('snapshot_date')
    ->get();

// arrays for chart
$labels = $history->pluck('snapshot_date')->toArray();
$trendScores = $history->pluck('trend_score')->toArray();
$jobOpenings = $history->pluck('job_openings')->toArray();
$searchVolumes = $history->pluck('search_volume')->toArray();


return view('dashboard.features.career_trends', [
    'user'          => $user,
    'careerName'    => $careerName,
    'trend'         => $trend,
    'labels'        => $labels,
    'trendScores'   => $trendScores,
    'jobOpenings'   => $jobOpenings,
    'searchVolumes' => $searchVolumes,
]);

    }

    /*
    |--------------------------------------------------------------------------
    | Manual refresh
    |--------------------------------------------------------------------------
    */
    public function refresh(Request $request)
    {
        $request->validate([
            'career_name' => 'required|string|min:2',
        ]);

        $careerName = trim($request->career_name);
        $region = 'Pakistan';

        $careerModel = Career::where('title', $careerName)->first();

        $trend = $this->refreshTrendFromAI($careerName, $region, $careerModel);

        return redirect()
            ->route('career.trends', ['career' => $careerName])
            ->with('success', 'Trend insight updated!');
    }

    /*
    |--------------------------------------------------------------------------
    | Call GROQ API to generate trend insight
    |--------------------------------------------------------------------------
    */
    protected function refreshTrendFromAI(string $careerName, string $region, ?Career $careerModel = null): CareerTrend
    {
        $apiKey = env('GROQ_API_KEY');

        $prompt = "
You are a professional global labour market analyst.

Analyze the current global + {$region} job market for the career:
\"{$careerName}\"

OUTPUT ONLY VALID JSON IN THIS STRUCTURE:

{
  \"demand_level\": \"High | Medium | Low\",
  \"trend_score\": 0-100,
  \"trend_direction\": \"Rising | Stable | Falling\",
  \"job_openings\": 1000,
  \"search_volume\": 20000,
  \"top_skills\": [\"skill1\", \"skill2\"],
  \"top_roles\": [\"role1\", \"role2\"],
  \"insight_summary\": \"2-4 lines summary of the current market trend.\"
}

Rules:
- No extra commentary
- No markdown
- JSON only
- Use realistic, data-backed estimates
";

$model = env('GROQ_MODEL', 'openai/gpt-oss-20b');

        // --- GROQ API CALL ---
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $model, // Recommended Groq model
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3,
        ]);

        if (!$response->successful()) {
            return CareerTrend::create([
                'career_id'       => $careerModel?->id,
                'career_name'     => $careerName,
                'region'          => $region,
                'demand_level'    => 'Unknown',
                'trend_score'     => null,
                'trend_direction' => null,
                'job_openings'    => null,
                'search_volume'   => null,
                'top_skills'      => [],
                'top_roles'       => [],
                'insight_summary' => 'Trend data temporarily unavailable.',
            ]);
        }

        $jsonText = $response->json()['choices'][0]['message']['content'] ?? '{}';

        // Attempt to decode
        $data = json_decode($jsonText, true);

        // If JSON is broken, fallback safe
        if (!is_array($data)) {
            $data = [];
        }

        $trend = CareerTrend::updateOrCreate(
            [
                'career_name' => $careerName,
                'region'      => $region,
            ],
            [
                'career_id'       => $careerModel?->id,
                'demand_level'    => $data['demand_level'] ?? 'Unknown',
                'trend_score'     => $data['trend_score'] ?? null,
                'trend_direction' => $data['trend_direction'] ?? null,
                'job_openings'    => $data['job_openings'] ?? null,
                'search_volume'   => $data['search_volume'] ?? null,
                'top_skills'      => $data['top_skills'] ?? [],
                'top_roles'       => $data['top_roles'] ?? [],
                'insight_summary' => $data['insight_summary'] ?? null,
            ]
        );
        
        // ✅ yahan snapshot save karo
        CareerTrendSnapshot::create([
            'career_trend_id' => $trend->id,
            'career_name'     => $careerName,
            'region'          => $region,
            'trend_score'     => $trend->trend_score,
            'job_openings'    => $trend->job_openings,
            'search_volume'   => $trend->search_volume,
            'snapshot_date'   => now()->toDateString(),
        ]);
        
        return $trend;

        
    }
    
}
