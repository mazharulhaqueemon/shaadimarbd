<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model


{    use HasFactory;
    protected $fillable = [
        'profile_id',
        'profession',
        'job_title',
        'company',
        'annual_income',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
