<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
     protected $fillable = [
        'profile_id',
        'present_address',
        'permanent_address',
        'city',
        'address',
        'nationality',
        'residence_status',
        'living_status',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
