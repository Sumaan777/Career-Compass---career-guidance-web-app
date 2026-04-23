<?php

// app/Models/JobSearch.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'degree',
        'skills',
        'field_of_interest',
        'experience_years',
        'location',
        'total_results',
        'ai_jobs',
    ];

    protected $casts = [
        'ai_jobs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

