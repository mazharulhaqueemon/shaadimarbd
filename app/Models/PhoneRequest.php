<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'receiver_id',
        'status',
    ];

    // Relationships
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
