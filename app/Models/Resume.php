<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    protected $fillable = [
        'user_id',
        'file_path',
        'original_name',
        'analysis_text'   // <-- THIS LINE MISSING
    ];

    public function analysis()
    {
        return $this->hasOne(ResumeAnalysisResult::class);
    }

    protected static function booted()
    {
        static::deleting(function ($resume) {
            $resume->analysis()->delete();
        });
    }
}
