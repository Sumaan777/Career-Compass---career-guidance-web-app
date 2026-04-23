<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resume;
use Auth;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. UPLOAD RESUME
    |--------------------------------------------------------------------------
    */
    public function upload(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,doc,docx|max:5000'
        ]);

        $userId = Auth::id();

        // Delete old resume
        $oldResume = Resume::where('user_id', $userId)->first();
        if ($oldResume) {
            if (Storage::disk('public')->exists($oldResume->file_path)) {
                Storage::disk('public')->delete($oldResume->file_path);
            }
            $oldResume->delete();
        }

        // Store new resume
        $file = $request->file('resume');
        $path = $file->store('resumes', 'public');

        Resume::create([
            'user_id'       => $userId,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'analysis_text' => null   // reset previous analysis
        ]);

        return back()->with('success', 'Resume uploaded successfully!');
    }



    /*
    |--------------------------------------------------------------------------
    | 2. ANALYZE RESUME & STORE RESULTS
    |--------------------------------------------------------------------------
    */
    public function analyze()
    {
        $resume = Resume::where('user_id', Auth::id())->first();
    
        if (!$resume) {
            return response()->json([
                'status' => 'error',
                'analysis' => 'No resume found.'
            ], 400);
        }

        // Extract Text
        $filePath = storage_path('app/public/' . $resume->file_path);
        $text = "";

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
        } catch (\Throwable $e) {
            $text = "";
        }

        $text = preg_replace('/[\x00-\x1F\x80-\xFF]/', ' ', $text);
        $text = trim($text);

        if (strlen($text) < 30) {
            $text = "Resume text could not be extracted.";
        }

        if (strlen($text) > 8000) {
            $text = substr($text, 0, 8000);
        }

        // Gemini API
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); // default also given

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $prompt = "
You are a professional resume reviewer.
Analyze this resume and provide:

- Professional Summary
- Strengths
- Weaknesses
- Missing Skills
- Suggested Job Roles
- Score out of 100

Resume:
{$text}
";

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json"
            ])->timeout(45)
            ->post($endpoint, [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ]
                ]
            ]);

            $json = $response->json();

            // Extract AI Response
            $finalText = "";
            if (isset($json["candidates"][0]["content"]["parts"])) {
                foreach ($json["candidates"][0]["content"]["parts"] as $part) {
                    if (isset($part["text"])) {
                        $finalText .= $part["text"] . "\n";
                    }
                }
            }

            if (trim($finalText) == "") {
                $finalText = "AI response not available.";
            }

            // Save in resume table
            $resume->update([
                'analysis_text' => trim($finalText)
            ]);

            return response()->json([
                'status' => 'success',
                'analysis' => trim($finalText)
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'analysis' => "AI error: " . $e->getMessage()
            ], 500);
        }
    }



    /*
    |--------------------------------------------------------------------------
    | 3. SHOW RESUME PAGE
    |--------------------------------------------------------------------------
    */
    public function show()
    {
        $resume = Resume::where('user_id', Auth::id())->first();
        return view('dashboard.features.resume', compact('resume'));
    }



    /*
    |--------------------------------------------------------------------------
    | 4. DELETE RESUME + ANALYSIS
    |--------------------------------------------------------------------------
    */
    public function delete()
    {
        $resume = Resume::where('user_id', Auth::id())->first();

        if (!$resume) {
            return back()->with('error', 'No resume found.');
        }

        if (Storage::disk('public')->exists($resume->file_path)) {
            Storage::disk('public')->delete($resume->file_path);
        }

        $resume->delete();

        return back()->with('success', 'Resume deleted successfully.');
    }
}
