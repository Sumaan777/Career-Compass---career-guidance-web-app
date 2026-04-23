<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerProgress extends Model
{
    protected $table = 'career_progresses'; // IMPORTANT FIX

    protected $fillable = [
        'user_id',
        'career_name',
        'progress_percent',
    ];

    public function tasks()
    {
        return $this->hasMany(ProgressTask::class, 'progress_id')->orderBy('order_number');
    }
}
