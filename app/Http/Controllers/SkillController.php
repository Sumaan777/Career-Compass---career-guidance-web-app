<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Skill;

class SkillController extends Controller
{
    // Add new skill
    public function store(Request $request)
    {
        $request->validate([
            'skill_name' => 'required|string|max:255',
            'proficiency' => 'nullable|string|max:255'
        ]);

        $profile = Auth::user()->profile;

        Skill::create([
            'profile_id' => $profile->id,
            'skill_name' => $request->skill_name,
            'proficiency' => $request->proficiency
        ]);

        return response()->json(['success' => true, 'message' => 'Skill added successfully']);
    }

    // Update skill
    public function update(Request $request, Skill $skill)
    {
        $request->validate([
            'skill_name' => 'required|string|max:255',
            'proficiency' => 'nullable|string|max:255'
        ]);

        // security: user should only update their own skill
        if ($skill->profile_id !== Auth::user()->profile->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $skill->update($request->only(['skill_name', 'proficiency']));

        return response()->json(['success' => true, 'message' => 'Skill updated successfully']);
    }

    // Delete skill
    public function destroy(Skill $skill)
    {
        if ($skill->profile_id !== Auth::user()->profile->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $skill->delete();

        return response()->json(['success' => true, 'message' => 'Skill deleted successfully']);
    }
}
