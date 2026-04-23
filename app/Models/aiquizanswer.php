<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIQuizAnswer extends Model
{
    use HasFactory;

    protected $table = 'ai_quiz_answers';

    protected $fillable = [
        'user_id',
        'ai_quiz_id',
        'answer_text',
    ];

    public function question()
    {
        return $this->belongsTo(AIQuiz::class, 'ai_quiz_id');
    }
}
