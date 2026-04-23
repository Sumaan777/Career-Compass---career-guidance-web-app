<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'user_type',
        'profile_photo',
        'is_questionnaire_completed',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function profile()
{
    return $this->hasOne(Profile::class);
}

public function aiResult()
{
    return $this->hasOne(AIResult::class);
}

public function roadmap()
{
    return $this->hasOne(Roadmap::class);
}
public function embedding()
{
    return $this->hasOne(\App\Models\UserEmbedding::class);
}

}

