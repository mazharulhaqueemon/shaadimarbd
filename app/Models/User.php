<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Plan;
use App\Models\PhoneRequest;
use App\Models\ProfilePicture;
use App\Models\Payment;
use App\Models\Profile;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;



class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'account_created_by',
        'otp',
        'otp_expires_at',
        'is_admin',
        'plan_id',
        'plan_activated_at',
        'phone_number',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'plan_activated_at' => 'datetime',
        ];
    }

    // Relationship: User belongs to a Plan
    public function plan(){
        return $this->belongsTo(Plan::class);
    }


    // Relationship: Phone requests sent by this user
    public function sentPhoneRequests(){
        return $this->hasMany(PhoneRequest::class, 'requester_id');
    }

    // Relationship: Phone requests received by this user
    public function receivedPhoneRequests(){
        return $this->hasMany(PhoneRequest::class, 'receiver_id');
    }

    // Relationship: User has many Profile Pictures
    public function profilePictures(){
        return $this->hasMany(ProfilePicture::class);
    }
    public function canAccessPanel(Panel $panel): bool
    {
        // Only allow users with is_admin = 1
        return $this->is_admin === 1;
    }

    // Relationship: User has one primary Profile Picture
    public function primaryProfilePicture(){
        return $this->hasOne(ProfilePicture::class)->where('is_primary', true);
    }

    // Relationship: User has many Payments
    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function profile(){
        return $this->hasOne(Profile::class);
    }



    // Automatically assign Free Plan on creation if plan_id is empty
    protected static function booted(){
        static::creating(function ($user) {
            if (empty($user->plan_id)) {
                $user->plan_id = 1; // Free Plan has ID = 1
            }
        });
    }
}
