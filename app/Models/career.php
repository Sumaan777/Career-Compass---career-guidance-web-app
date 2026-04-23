<?php

// app/Models/Career.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Career extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'short_description',
        'skills_tags',
    ];

    protected $casts = [
        'skills_tags' => 'array',
    ];
}

