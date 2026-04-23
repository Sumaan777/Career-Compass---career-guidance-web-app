<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'profile_id',
        'skill_name',
        'proficiency',
    ];

    // Skill belongs to a Profile
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
