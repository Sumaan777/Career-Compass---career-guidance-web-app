<?php

// app/Models/JobRecommendation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'degree',
        'skills',
        'field_of_interest',
        'experience_years',
        'location',
        'ai_job_title',
        'reason',
        'source',
        'job_title',
        'company',
        'job_location',
        'redirect_url',
        'salary',
        'posted_at',
        'match_score',
        'raw_api',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'raw_api'   => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

