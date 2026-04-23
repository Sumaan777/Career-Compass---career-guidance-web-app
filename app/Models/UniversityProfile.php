<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniversityProfile extends Model
{
    protected $fillable = [
        'profile_id',
        'university_name',
        'degree_program',
        'current_semester',
        'cgpa',
        'interests',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}

