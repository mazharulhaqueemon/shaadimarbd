<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class TrackPhoneRequest extends Model
{
    protected $table = 'track_phone_requests';

    protected $fillable = [
        'viewer_user_id',
        'viewed_user_ids',
        'count',
    ];

    protected $casts = [
        // Laravel 9+ supports 'array' cast; AsArrayObject is optionally used for mutability
        'viewed_user_ids' => 'array',
        'count' => 'integer',
    ];

    // Relation to the user who views phone numbers
    public function viewer()
    {
        return $this->belongsTo(\App\Models\User::class, 'viewer_user_id');
    }
}
