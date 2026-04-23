<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Profile;

class AiSimilarUserController extends Controller
{
    /**
     * Fetch AI-based similar users (ajax)
     */
    public function fetchAjax()
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['similar' => []]);
        }

        // STEP 1 — Build a vector text
        $baseText = strtolower(
            ($profile->field_of_interest ?? '') . " " .
            ($profile->skills ?? '') . " " .
            ($profile->education_level ?? '') . " " .
            ($profile->current_status ?? '')
        );

        // STEP 2 — Load all other profiles
        $others = Profile::where('user_id', '!=', $user->id)
            ->with('user')
            ->get();

        $similarUsers = [];

        foreach ($others as $p) {
            $compareText = strtolower(
                ($p->field_of_interest ?? '') . " " .
                ($p->skills ?? '') . " " .
                ($p->education_level ?? '') . " " .
                ($p->current_status ?? '')
            );

            // STEP 3 — AI semantic matching
            $score = $this->aiSimilarity($baseText, $compareText);

            if ($score >= 0.55) {
                $similarUsers[] = [
                    'id' => $p->user->id,
                    'name' => $p->user->name,
                    'field_of_interest' => $p->field_of_interest,
                    'similarity' => round($score * 100),
                ];
            }
        }

        // Top 5 only
        usort($similarUsers, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        $similarUsers = array_slice($similarUsers, 0, 5);

        return response()->json([
            'similar' => $similarUsers,
            'count' => count($similarUsers)
        ]);
    }

    /**
     * AI similarity scoring using GROQ
     */
    private function aiSimilarity($textA, $textB)
    {
        $apiKey = env('GROQ_API_KEY');

        if (!$apiKey) {
            // fallback to simple similarity
            return similar_text($textA, $textB) / 100;
        }

        $prompt = "
Compare the following two user profiles and return ONLY a number between 0 and 1
where 1 means extremely similar and 0 means no similarity.

Profile A:
{$textA}

Profile B:
{$textB}

Return ONLY the number. No words, no explanation.
";

$model = env('GROQ_MODEL', 'openai/gpt-oss-20b');

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => "Bearer {$apiKey}"
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                "model" => $model,
                "messages" => [
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0
            ]);

            $val = trim($response->json()['choices'][0]['message']['content'] ?? '0');

            return floatval($val);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
