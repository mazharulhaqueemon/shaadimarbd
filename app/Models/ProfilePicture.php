<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProfilePicture extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor: get full public URL for responses
    public function getImageUrlAttribute()
    {
        return $this->image_path ? Storage::disk('s3')->url($this->image_path) : null;
    }

    // If you want JSON to include image_url automatically:
    protected $appends = ['image_url'];
}
