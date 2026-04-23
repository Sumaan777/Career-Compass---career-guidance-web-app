<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIQuiz extends Model
{
    use HasFactory;

    protected $table = 'ai_quizzes';

    protected $fillable = [
        'user_id',
        'career_quiz_id',
        'question_text',
        'question_order'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
