<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewMessage extends Model
{
    protected $fillable = [
        'session_id', 'role', 'message', 'score'
    ];

    public function session()
    {
        return $this->belongsTo(InterviewSession::class, 'session_id');
    }
}
