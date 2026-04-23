<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'date_of_birth',
        'gender',
        'education_level',
        'current_status',
        'field_of_interest',
        'experience_years',
        'resume_path',
        'profile_photo',
        'location'
    ];

protected static function booted()
{
    static::creating(function ($profile) {
        if (!$profile->full_name) {
            $profile->full_name = Auth::user()->name ?? 'User';
        }

        if (!$profile->current_status) {
            $profile->current_status = Auth::user()->user_type ?? 'undecided';
        }
    });
}


    // Relation with User
    public function educations()
    {
        return $this->hasMany(Education::class);
    }
    
    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    

    public function highschoolProfile()
{
    return $this->hasOne(HighSchoolProfile::class);
}

public function universityProfile()
{
    return $this->hasOne(UniversityProfile::class);
}

public function graduateProfile()
{
    return $this->hasOne(GraduateProfile::class);
}

public function switcherProfile()
{
    return $this->hasOne(SwitcherProfile::class);
}

public function undecidedProfile()
{
    return $this->hasOne(UndecidedProfile::class);
}
public function skills()
{
    return $this->hasMany(Skill::class);
}


}
