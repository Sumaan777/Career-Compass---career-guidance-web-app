<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmbedding extends Model
{
    protected $fillable = [
        'user_id',
        'vector',
    ];

    protected $casts = [
        'vector' => 'array', // JSON -> array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
