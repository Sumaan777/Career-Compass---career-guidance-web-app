<?php

// app/Models/CareerTrend.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CareerTrendSnapshot;


class CareerTrend extends Model
{
    use HasFactory;

    protected $fillable = [
        'career_id',
        'career_name',
        'region',
        'demand_level',
        'trend_score',
        'trend_direction',
        'job_openings',
        'search_volume',
        'top_skills',
        'top_roles',
        'insight_summary',
    ];

    protected $casts = [
        'top_skills' => 'array',
        'top_roles'  => 'array',
    ];

    public function career()
    {
        return $this->belongsTo(Career::class);
    }
    public function snapshots()
{
    return $this->hasMany(CareerTrendSnapshot::class, 'career_trend_id');
}

}


