<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyDetail extends Model
{
     protected $fillable = [
        'profile_id',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'brothers_unmarried',
        'brothers_married',
        'sisters_unmarried',
        'sisters_married',
        'family_details',
    ];
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
