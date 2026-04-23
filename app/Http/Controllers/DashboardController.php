<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (is_null($user->email_verified_at)) {
            return redirect('/verify-email-otp');
        }
        

        // If user hasn't selected career type → go onboarding
        if (empty($user->user_type)) {
            return redirect()->route('onboarding.show');
        }

        return view('dashboard.main', compact('user'));
    }
    public function search(Request $request)
{
    $q = strtolower(trim($request->query('q')));

    if (!$q || strlen($q) < 1) {
        return response()->json(["results" => []]);
    }

    $features = [
        [
            "label" => "Career Quiz",
            "match" => ["quiz", "career quiz"],
            "url" => route("ai.quiz.start"),
            "icon" => "bi bi-question-circle",
            "description" => "Find careers that match your personality"
        ],
        [
            "label" => "Skill Gap Analyzer",
            "match" => ["skill", "gap"],
            "url" => route("skill-gap.index"),
            "icon" => "bi bi-bar-chart-steps",
            "description" => "See missing skills for your target career"
        ],
        [
            "label" => "Resume Analyzer",
            "match" => ["resume", "cv"],
            "url" => route("resume.index"),
            "icon" => "bi bi-file-earmark-text",
            "description" => "Let AI review and improve your resume"
        ],
        [
            "label" => "Interview Simulator",
            "match" => ["interview", "mock"],
            "url" => route("interview.page"),
            "icon" => "bi bi-mic",
            "description" => "Practice real interviews with AI"
        ],
        [
            "label" => "Career Roadmap",
            "match" => ["roadmap", "path"],
            "url" => route("career.roadmap"),
            "icon" => "bi bi-map",
            "description" => "Get step-by-step roadmap for your goals"
        ],
        [
            "label" => "AI Career Chat",
            "match" => ["chat", "ai", "assistant"],
            "url" => route("career.chat"),
            "icon" => "bi bi-chat-dots",
            "description" => "Ask AI anything related to your career"
        ],
        [
            "label" => "Job Recommender",
            "match" => ["job", "work"],
            "url" => route("jobs.recommender"),
            "icon" => "bi bi-briefcase",
            "description" => "Find the best jobs for your skills"
        ],
        [
            "label" => "Career Trends",
            "match" => ["trend", "market"],
            "url" => route("career.trends"),
            "icon" => "bi bi-graph-up-arrow",
            "description" => "See real-time trending careers"
        ],
    ];

    $results = [];

    foreach ($features as $f) {
        foreach ($f["match"] as $m) {
            if (str_contains($f["label"]." ".$m, $q)) {

                $results[] = [
                    "label" => $f["label"],
                    "url"   => $f["url"],
                    "icon"  => $f["icon"],
                    "description" => $f["description"] // ⭐ added
                ];

                break;
            }
        }
    }

    return response()->json(["results" => $results]);
}


}
