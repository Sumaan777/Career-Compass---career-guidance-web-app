<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Profile;
use App\Models\UserEmbedding;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // 1️⃣ Show profile page
    public function settings()
    {
        $profile = Profile::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'full_name' => Auth::user()->name ?? 'User',
                'current_status' => Auth::user()->user_type ?? 'undecided',
            ]
        );
    
        $profile->load([
            'skills',
            'educations',
            'experiences',
            'highschoolProfile',
            'universityProfile',
            'graduateProfile',
            'switcherProfile',
            'undecidedProfile',
        ]);
    
        return view('dashboard.features.profile_settings', compact('profile'));
    }
    

    // 2️⃣ Update profile info (AJAX) + AI embedding update
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name'         => 'required|string|max:255',
                'field_of_interest' => 'nullable|string|max:255',
                'education_level'   => 'nullable|string|max:255',
                'current_status'    => 'nullable|string|max:255',
                'location'          => 'nullable|string|max:255',
            ]);

            $user = Auth::user();
            $profile = Profile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $user->name ?? 'User',
                    'current_status' => $user->user_type ?? 'undecided',
                ]
            );
            

            // Update profile fields
            $profile->update(array_filter($validated));

            // Mark profile as completed
            $user->profile_completed = 1;
            $user->save();

            /*
            |---------------------------------------------------------
            | 🔥 STEP 5: Update AI Embedding for "Similar Users"
            |----------------------------------------------------------
            | - Build a text summary of the user + profile
            | - Send to Gemini embedding API
            | - Store/update in user_embeddings
            |---------------------------------------------------------*/
            try {
                $summaryText = $this->buildUserProfileText($user, $profile);
                $vector = $this->generateEmbedding($summaryText);

                if ($vector) {
                    UserEmbedding::updateOrCreate(
                        ['user_id' => $user->id],
                        ['vector'  => $vector]
                    );
                }
                // If embedding fails, we silently ignore so profile update still succeeds
            } catch (\Exception $e) {
                // Optional: log error if you want
                // \Log::error('Embedding generation failed: ' . $e->getMessage());
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Profile updated successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 3️⃣ ADD / UPDATE EDUCATION
    public function addEducation(Request $request)
    {
        $profile = Auth::user()->profile;

        $data = $request->validate([
            'institution'    => 'required|string|max:255',
            'degree'         => 'nullable|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date',
            'is_current'     => 'nullable|boolean',
        ]);

        $existing = $profile->educations()->first();

        if ($existing) {
            $existing->update($data);
            $message = 'Education updated successfully.';
        } else {
            $profile->educations()->create($data);
            $message = 'Education added successfully.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    // 4️⃣ ADD / UPDATE EXPERIENCE
    public function addExperience(Request $request)
    {
        $profile = Auth::user()->profile;

        $data = $request->validate([
            'job_title'  => 'required|string|max:255',
            'company'    => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
            'is_current' => 'nullable|boolean',
            'description'=> 'nullable|string'
        ]);

        $existing = $profile->experiences()->first();

        if ($existing) {
            $existing->update($data);
            $message = 'Experience updated successfully.';
        } else {
            $profile->experiences()->create($data);
            $message = 'Experience added successfully.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    // 5️⃣ Used by routing previously
    public function index()
    {
        $profile = Profile::where('user_id', Auth::id())
            ->with(['skills', 'educations', 'experiences'])
            ->first();

        return view('dashboard.features.profile_settings', compact('profile'));
    }

    /*
    |--------------------------------------------------------------------------
    | 🔧 PRIVATE HELPERS FOR AI SIMILAR USERS FEATURE
    |--------------------------------------------------------------------------
    */

    /**
     * Build a text / JSON summary from user + profile
     * This is what we send to Gemini to create an embedding.
     */
    private function buildUserProfileText($user, Profile $profile): string
    {
        // Handle skills whether it's a string column or a relation
        $skillsValue = $profile->skills ?? '';

        // If it's a relation/collection (e.g., Skill model), convert to comma string
        if (is_iterable($skillsValue) && !is_string($skillsValue)) {
            $skillNames = [];
            foreach ($skillsValue as $skill) {
                // Adjust key depending on your Skill model
                $skillNames[] = $skill->skill_name ?? (string) $skill;
            }
            $skillsValue = implode(', ', $skillNames);
        }

        $data = [
            'name'            => $user->name,
            'user_type'       => $user->type ?? '',
            'career_goal'     => $user->career_suggestion ?? '',
            'education_level' => $profile->education_level ?? '',
            'current_status'  => $profile->current_status ?? '',
            'field_of_interest'=> $profile->field_of_interest ?? '',
            'skills'          => $skillsValue,
            'location'        => $profile->location ?? '',
        ];

        // JSON string is good for embeddings because it's structured
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Call Gemini Embedding API and return vector array or null on failure.
     */
    private function generateEmbedding(string $text): ?array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return null;
        }

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'x-goog-api-key'=> $apiKey,
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedText',
            ['text' => $text]
        );

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        // Gemini embedding structure: { "embedding": { "value": [ ... ] } }
        return $data['embedding']['value'] ?? null;
    }

 // 6️⃣ Update profile photo   
    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);
    
        $profile = Auth::user()->profile;
    
        if (!$profile) {
            return back()->withErrors(['Profile not found']);
        }
    
        // 🔥 Delete old photo (if exists)
        if ($profile->profile_photo && Storage::disk('public')->exists($profile->profile_photo)) {
            Storage::disk('public')->delete($profile->profile_photo);
        }
    
        // 🔥 Store new photo
        $path = $request->file('profile_photo')
                       ->store('profile_photos', 'public');
    
        // 🔥 Save path in DB
        $profile->profile_photo = $path;
        $profile->save();
    
        return redirect()->back()->with('success', 'Profile photo updated successfully!');
    }

    public function removePhoto()
{
    $profile = Auth::user()->profile;

    if ($profile->profile_photo) {
        Storage::disk('public')->delete($profile->profile_photo);
        $profile->update(['profile_photo' => null]);
    }

    return redirect()
        ->back()
        ->with('success', 'Profile photo removed successfully');
}

    
}
