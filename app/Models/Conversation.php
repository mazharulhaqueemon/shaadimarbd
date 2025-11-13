<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'user_one_id',
        'user_two_id',
    ];

    /**
     * Ensure consistent ordering (lower id in user_one_id).
     * Use this method when creating or searching conversations
     * to guarantee uniqueness for a pair.
     *
     * Example: Conversation::getOrCreateBetween($a, $b)
     */
    public static function normalizePair(int $a, int $b): array
    {
        if ($a <= $b) {
            return [$a, $b];
        }

        return [$b, $a];
    }

    /**
     * Get existing conversation between two users or create one.
     */
    public static function getOrCreateBetween(int $userIdA, int $userIdB): self
    {
        [$one, $two] = self::normalizePair($userIdA, $userIdB);

        return self::firstOrCreate([
            'user_one_id' => $one,
            'user_two_id' => $two,
        ]);
    }

    /**
     * Scope to find conversation between two users.
     */
    public function scopeBetween(Builder $query, int $userA, int $userB): Builder
    {
        [$one, $two] = self::normalizePair($userA, $userB);

        return $query->where('user_one_id', $one)
                     ->where('user_two_id', $two);
    }

    /* ---------------------
       Relationships
       --------------------- */

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    /**
     * Convenience: return the "other" user id given a user id.
     */
    public function otherUserId(int $userId): ?int
    {
        if ($this->user_one_id === $userId) {
            return $this->user_two_id;
        }

        if ($this->user_two_id === $userId) {
            return $this->user_one_id;
        }

        return null;
    }
}
