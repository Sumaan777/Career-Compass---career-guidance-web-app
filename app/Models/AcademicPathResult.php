<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicPathResult extends Model
{
    protected $fillable = [
        'user_id',
        'target_career',
        'education_match',
        'required_degrees',
        'recommended_paths',
        'certifications',
        'summary'
    ];

    protected $casts = [
        'education_match' => 'array',
        'required_degrees' => 'array',
        'recommended_paths' => 'array',
        'certifications' => 'array'
    ];
}
