<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\HighSchoolProfile;
use App\Models\UniversityProfile;
use App\Models\GraduateProfile;
use App\Models\SwitcherProfile;
use App\Models\UndecidedProfile;
use Illuminate\Support\Facades\Auth;

class AIPromptBuilder
{
    public static function build()
    {
        $user = Auth::user();
        $profile = $user->profile;

        // ==========================
        // BASIC PROFILE INFORMATION
        // ==========================
        $data = [
            "basic" => [
                "name" => $user->name,
                "email" => $user->email,
                "user_type" => $user->user_type,
            ],

            "profile" => [
                "full_name" => $profile->full_name ?? null,
                "education_level" => $profile->education_level ?? null,
                "current_status" => $profile->current_status ?? null,
                "field_of_interest" => $profile->field_of_interest ?? null,
                "location" => $profile->location ?? null,
            ],
        ];

        // ==========================
        // USER TYPE SPECIFIC DATA
        // ==========================
        switch ($user->user_type) {

            case "high_school":
                $data["high_school"] = HighSchoolProfile::where("profile_id", $profile->id)->first();
                break;

            case "student":
                $data["university"] = UniversityProfile::where("profile_id", $profile->id)->first();
                break;

            case "fresh_graduate":
                $data["graduate"] = GraduateProfile::where("profile_id", $profile->id)->first();
                break;

            case "career_switcher":
                $data["switcher"] = SwitcherProfile::where("profile_id", $profile->id)->first();
                break;

            case "undecided":
                $data["undecided"] = UndecidedProfile::where("profile_id", $profile->id)->first();
                break;
        }

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
