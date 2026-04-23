<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIChat extends Model
{
    protected $table = 'ai_chats';

    protected $fillable = [
        'user_id',
        'role',
        'message'
    ];
}
