<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerReport extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'summary',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
