<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    // Centralized status constants
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_method',
        'transaction_id',
        'sender_name',
        'sender_phone',
        'screenshot_path',
        'status',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING, 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Optional accessor for screenshot URL
    public function getScreenshotUrlAttribute()
    {
        return $this->screenshot_path
            ? Storage::disk(config('filesystems.default'))->url($this->screenshot_path)
            : null;
    }

    protected $appends = ['screenshot_url'];
}
