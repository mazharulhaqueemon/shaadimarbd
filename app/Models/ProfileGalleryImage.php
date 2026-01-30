<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileGalleryImage extends Model
{
    protected $fillable = [
        'profile_id',
        'path',
    ];
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
