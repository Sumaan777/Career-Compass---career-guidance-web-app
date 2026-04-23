<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GraduateProfile extends Model
{
    protected $fillable = [
        'profile_id',
        'university_name',
        'degree_name',
        'major',
        'graduation_year',
        'cgpa',
        'final_project_title',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}

