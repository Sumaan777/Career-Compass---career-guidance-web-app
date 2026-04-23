<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeAnalysisResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'summary',
        'strengths',
        'weaknesses',
        'missing_skills',
        'suggested_roles',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
