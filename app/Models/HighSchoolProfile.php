<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HighSchoolProfile extends Model
{
    protected $fillable = [
        'profile_id',
        'school_name',
        'class_level',
        'academic_interests',
        'strengths',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
