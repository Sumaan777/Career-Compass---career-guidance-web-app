<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UndecidedProfile extends Model
{
    protected $fillable = [
        'profile_id',
        'interests',
        'strengths',
        'motivation_level',
        'preferred_learning_style',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
