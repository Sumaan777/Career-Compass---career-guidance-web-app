<?php

namespace App\Http\Controllers;

use App\Models\CareerReport;
use App\Models\Profile;
use App\Models\SkillGapAnalysis;
use App\Models\AcademicPathResult;
use App\Models\LearningResource;
use App\Models\CareerTrend;
use App\Models\InterviewSession;
use App\Models\Roadmap;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CareerReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. Report Page + History
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = Auth::user();

        $latestReport = CareerReport::where('user_id', $user->id)->latest()->first();
        $history = CareerReport::where('user_id', $user->id)->latest()->take(10)->get();

        return view('dashboard.features.career_report', compact('user', 'latestReport', 'history'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Generate PDF Report
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request)
{
    $user = Auth::user();
    $profile = $user->profile ?? null;

    // Fetch latest modules
    $skillGap     = SkillGapAnalysis::where('user_id', $user->id)->latest()->first();
    $academicPath = AcademicPathResult::where('user_id', $user->id)->latest()->first();
    $learningResources = LearningResource::where('user_id', $user->id)->latest()->take(8)->get();
    $roadmap      = Roadmap::where('user_id', $user->id)->latest()->first();
    $interview    = InterviewSession::where('user_id', $user->id)->latest()->first();
    $resume       = Resume::where('user_id', $user->id)->latest()->first();

    // Determine target career
    $targetCareer = $skillGap->target_career
        ?? ($academicPath->target_career ?? ($roadmap->career ?? null));

    // Career Trend
    $trend = $targetCareer
        ? CareerTrend::where('career_name', $targetCareer)->latest()->first()
        : null;

    /*
    |--------------------------------------------------------------------------
    | Generate SVG Trend Chart (NEW SECTION)
    |--------------------------------------------------------------------------
    */
    if ($trend) {

        // Ensure folder exists
        Storage::disk('public')->makeDirectory('trend_charts');

        $svg = "
        <svg width='500' height='200' xmlns='http://www.w3.org/2000/svg'>
            <rect width='100%' height='100%' fill='#eef4ff'/>
            <text x='20' y='40' font-size='18' fill='#0d6efd'>
                Trend Score: {$trend->trend_score}
            </text>

            <text x='20' y='80' font-size='16' fill='#333'>
                Job Openings: {$trend->job_openings}
            </text>

            <text x='20' y='120' font-size='16' fill='#333'>
                Search Volume: {$trend->search_volume}
            </text>

            <text x='20' y='160' font-size='14' fill='#666'>
                Direction: {$trend->trend_direction}
            </text>
        </svg>";

        Storage::disk('public')->put("trend_charts/{$user->id}.svg", $svg);
    }

    /*
    |--------------------------------------------------------------------------
    | Build context for AI summary
    |--------------------------------------------------------------------------
    */
    $context = [
        'user' => [
            'name'  => $user->name,
            'email' => $user->email,
        ],
        'profile' => $profile ? [
            'full_name'       => $profile->full_name,
            'education_level' => $profile->education_level,
            'field_of_interest'=> $profile->field_of_interest,
            'skills'          => $profile->skills,
            'experience_years'=> $profile->experience_years,
            'location'        => $profile->location,
        ] : null,

        'target_career' => $targetCareer,

        'skill_gap' => $skillGap ? [
            'required_skills' => $skillGap->required_skills ?? [],
            'matched_skills'  => $skillGap->matched_skills ?? [],
            'missing_skills'  => $skillGap->missing_skills ?? [],
            'extra_skills'    => $skillGap->extra_skills ?? [],
        ] : null,

        'academic_path' => $academicPath ? [
            'eligible'        => $academicPath->education_match['eligible'] ?? null,
            'reasons'         => $academicPath->education_match['reasons'] ?? [],
            'required_degrees'=> $academicPath->required_degrees ?? [],
            'certifications'  => $academicPath->certifications ?? [],
        ] : null,

        'learning_resources' => $learningResources->map(fn($r) => [
            'skill'    => $r->skill,
            'title'    => $r->title,
            'platform' => $r->platform,
        ]),

        'trend' => $trend ? [
            'demand_level'    => $trend->demand_level,
            'trend_score'     => $trend->trend_score,
            'trend_direction' => $trend->trend_direction,
            'job_openings'    => $trend->job_openings,
            'search_volume'   => $trend->search_volume,
            'top_skills'      => $trend->top_skills,
            'top_roles'       => $trend->top_roles,
        ] : null,

        'interview' => $interview ? [
            'field'          => $interview->field,
            'total_score'    => $interview->total_score,
            'question_count' => $interview->question_count,
            'final_report'   => $interview->final_report,
        ] : null,

        'roadmap' => $roadmap ? [
            'career'  => $roadmap->career,
            'summary' => $roadmap->summary,
        ] : null,
    ];

    /*
    |--------------------------------------------------------------------------
    | Groq AI Summary
    |--------------------------------------------------------------------------
    */
    $aiSummary = "AI summary is not available.";

    try {
        $apiKey = env('GROQ_API_KEY');

        if ($apiKey) {
            $prompt = "
Generate a detailed career report with these sections:

1. Profile Overview
2. Career Fit Summary
3. Skill Gap Summary
4. Academic Eligibility
5. Market Outlook
6. Interview Readiness
7. 90-Day Action Plan

Use this data:

" . json_encode($context) . "

Write plain text only. No JSON.
";

$model = env('GROQ_MODEL', 'openai/gpt-oss-20b');

            $response = Http::withHeaders([
                "Authorization" => "Bearer $apiKey",
                "Content-Type"  => "application/json",
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                "model" => $model,
                "messages" => [
                    ["role" => "user", "content" => $prompt],
                ],
            ]);

            $aiSummary = $response->json()['choices'][0]['message']['content'] ?? $aiSummary;
        }
    } catch (\Exception $e) {
        $aiSummary = "AI summary could not be generated.";
    }

    /*
    |--------------------------------------------------------------------------
    | Generate & Save PDF
    |--------------------------------------------------------------------------
    */
    $title = 'Career Report - ' . now()->format('d M Y');
    $fileName = "career_report_{$user->id}_" . now()->format('Ymd_His') . ".pdf";
    $filePath = "reports/$fileName";

    Storage::disk('public')->makeDirectory('reports');

    $pdf = Pdf::loadView('pdf.career_report', [
        'user'              => $user,
        'profile'           => $profile,
        'context'           => $context,
        'aiSummary'         => $aiSummary,
        'skillGap'          => $skillGap,
        'academicPath'      => $academicPath,
        'learningResources' => $learningResources,
        'trend'             => $trend,
        'interview'         => $interview,
        'roadmap'           => $roadmap,
    ]);

    Storage::disk('public')->put($filePath, $pdf->output());

    CareerReport::create([
        'user_id'   => $user->id,
        'title'     => $title,
        'summary'   => mb_substr(strip_tags($aiSummary), 0, 250),
        'file_path' => $filePath,
    ]);

    return response()->download(storage_path("app/public/$filePath"));
}

    
    public function view($id)
{
    $report = CareerReport::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    $path = storage_path('app/public/' . $report->file_path);

    if (!file_exists($path)) {
        return back()->with('error', 'Report file not found.');
    }

    // Serve PDF directly from Laravel (no permission issues)
    return response()->file($path);
}
public function delete($id)
{
    $report = CareerReport::findOrFail($id);

    // Delete file from storage
    if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
        Storage::disk('public')->delete($report->file_path);
    }

    // Delete record from DB
    $report->delete();

    return back()->with('success', 'Report deleted successfully.');
}


}
