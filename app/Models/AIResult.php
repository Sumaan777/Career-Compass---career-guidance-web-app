<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIResult extends Model
{
    protected $table = 'ai_results'; // agar table ka yehi naam hai

    protected $fillable = [
        'user_id',
        'career_suggestion',   // yahan AI ka suggest kia gaya career
        'strengths',
        'personality',
        'raw_response',        // optional JSON
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
