<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressTask extends Model
{
    protected $table = 'progress_tasks'; // IMPORTANT FIX

    protected $fillable = [
        'progress_id',
        'task_title',
        'task_description',
        'phase_name',
        'is_completed',
        'order_number'
    ];

    public function progress()
    {
        return $this->belongsTo(CareerProgress::class, 'progress_id');
    }
}
