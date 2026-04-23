<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $table = 'experiences'; // 👈 Add this line

    protected $fillable = [
        'profile_id', 'job_title', 'company', 'start_date',
        'end_date', 'is_current', 'description'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}

