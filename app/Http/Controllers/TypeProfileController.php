<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HighSchoolProfile;
use App\Models\UniversityProfile;
use App\Models\GraduateProfile;
use App\Models\SwitcherProfile;
use App\Models\UndecidedProfile;

class TypeProfileController extends Controller
{
    protected function getProfile()
    {
        return Auth::user()->profile;
    }
    
    
    public function saveHighSchool(Request $request)
    {
        $profile = $this->getProfile();

        $data = $request->validate([
            'school_name'        => 'nullable|string|max:255',
            'class_level'        => 'nullable|string|max:50',
            'academic_interests' => 'nullable|string',
            'strengths'          => 'nullable|string',
        ]);

        $record = $profile->highschoolProfile ?: new HighSchoolProfile(['profile_id' => $profile->id]);
        $record->fill($data)->save();

        return response()->json(['success' => true, 'message' => 'High school profile saved.']);
    }

    public function saveUniversity(Request $request)
    {
        $profile = $this->getProfile();

        $data = $request->validate([
            'university_name'  => 'nullable|string|max:255',
            'degree_program'   => 'nullable|string|max:255',
            'current_semester' => 'nullable|string|max:50',
            'cgpa'             => 'nullable|numeric|min:0|max:4',
            'interests'        => 'nullable|string',
        ]);

        $record = $profile->universityProfile ?: new UniversityProfile(['profile_id' => $profile->id]);
        $record->fill($data)->save();

        return response()->json(['success' => true, 'message' => 'University profile saved.']);
    }

    public function saveGraduate(Request $request)
    {
        $profile = $this->getProfile();

        $data = $request->validate([
            'university_name'    => 'nullable|string|max:255',
            'degree_name'        => 'nullable|string|max:255',
            'major'              => 'nullable|string|max:255',
            'graduation_year'    => 'nullable|integer|min:1950|max:' . date('Y'),
            'cgpa'               => 'nullable|numeric|min:0|max:4',
            'final_project_title'=> 'nullable|string|max:255',
        ]);

        $record = $profile->graduateProfile ?: new GraduateProfile(['profile_id' => $profile->id]);
        $record->fill($data)->save();

        return response()->json(['success' => true, 'message' => 'Graduate profile saved.']);
    }

    public function saveSwitcher(Request $request)
    {
        $profile = $this->getProfile();

        $data = $request->validate([
            'current_field'        => 'nullable|string|max:255',
            'previous_field'       => 'nullable|string|max:255',
            'past_experience_years'=> 'nullable|integer|min:0|max:50',
            'skills_json'          => 'nullable|array',
            'certifications_json'  => 'nullable|array',
            'past_roles_json'      => 'nullable|array',
        ]);

        // arrays to json automatically because of cast
        $record = $profile->switcherProfile ?: new SwitcherProfile(['profile_id' => $profile->id]);
        $record->fill($data)->save();

        return response()->json(['success' => true, 'message' => 'Career switcher profile saved.']);
    }

    public function saveUndecided(Request $request)
    {
        $profile = $this->getProfile();

        $data = $request->validate([
            'interests'              => 'nullable|string',
            'strengths'              => 'nullable|string',
            'motivation_level'       => 'nullable|string|max:50',
            'preferred_learning_style'=> 'nullable|string|max:100',
        ]);

        $record = $profile->undecidedProfile ?: new UndecidedProfile(['profile_id' => $profile->id]);
        $record->fill($data)->save();

        return response()->json(['success' => true, 'message' => 'General profile saved.']);
    }
}
