<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
     protected $fillable = [
        'user_id',
        'gender',
        'dob',
        'marital_status',
        'height_feet',
        'weight_kg',
        'blood_group',
        'mother_tongue',
        'religion',
        'caste',
        'sub_caste',
        'bio',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}

public function education()
{
    return $this->hasMany(Education::class);
}

public function career()
{
    return $this->hasMany(Career::class);
}

public function familyDetail()
{
    return $this->hasOne(FamilyDetail::class);
}

public function location()
{
    return $this->hasOne(Location::class);
}

public function lifestyle()
{
    return $this->hasOne(Lifestyle::class);
}

public function partnerPreference()
{
    return $this->hasOne(PartnerPreference::class);
}

public function photos()
{
    return $this->hasMany(ProfilePicture::class, 'user_id', 'user_id');
}




}
