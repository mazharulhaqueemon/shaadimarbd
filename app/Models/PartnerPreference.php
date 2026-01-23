<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerPreference extends Model
{
    protected $fillable = [
        'profile_id',
        'preferred_age_min',
        'preferred_age_max',
        'preferred_height_min',
        'preferred_height_max',
        'preferred_religion',
        'preferred_caste',
        'preferred_education',
        'preferred_country',
        'preferred_profession',
        'other_expectations',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
