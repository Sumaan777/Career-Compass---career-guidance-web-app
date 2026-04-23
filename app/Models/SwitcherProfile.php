<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwitcherProfile extends Model
{
    protected $fillable = [
        'profile_id',
        'current_field',
        'previous_field',
        'past_experience_years',
        'skills_json',
        'certifications_json',
        'past_roles_json',
    ];

    protected $casts = [
        'skills_json' => 'array',
        'certifications_json' => 'array',
        'past_roles_json' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}

