<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningResource extends Model
{
    protected $fillable = [
        'user_id',
        'skill',
        'title',
        'platform',
        'url',
        'description',
        'difficulty',
        'duration',
    ];
}
