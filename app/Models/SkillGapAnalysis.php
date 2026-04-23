<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillGapAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_career',
        'current_skills',
        'required_skills',
        'missing_skills',
        'matched_skills',
        'extra_skills',
        'raw_ai_response',
    ];

    protected $casts = [
        'current_skills'   => 'array',
        'required_skills'  => 'array',
        'missing_skills'   => 'array',
        'matched_skills'   => 'array',
        'extra_skills'     => 'array',
        'raw_ai_response'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
