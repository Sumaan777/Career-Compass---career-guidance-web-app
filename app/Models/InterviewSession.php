<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewSession extends Model
{
    protected $fillable = [
        'user_id', 'field', 'status', 'total_score', 'question_count', 'final_report'
    ];

    public function messages()
    {
        return $this->hasMany(InterviewMessage::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
