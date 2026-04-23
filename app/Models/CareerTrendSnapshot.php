<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerTrendSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'career_trend_id',
        'career_name',
        'region',
        'trend_score',
        'job_openings',
        'search_volume',
        'snapshot_date',
    ];

    public function trend()
    {
        return $this->belongsTo(CareerTrend::class, 'career_trend_id');
    }
}

