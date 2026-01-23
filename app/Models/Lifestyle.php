<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lifestyle extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'diet',
        'smoking',
        'drinking',
        'hobbies',
    ];
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

}
