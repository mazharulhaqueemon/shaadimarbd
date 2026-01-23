<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'message',
        'status',     // e.g., 'sent', 'delivered', 'read'
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /* ---------------------
       Relationships
       --------------------- */

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /* ---------------------
       Scopes & helpers
       --------------------- */

    public function scopeUnread($query, int $userId)
    {
        return $query->where('receiver_id', $userId)
                     ->whereNull('read_at');
    }

    public function markAsRead(): self
    {
        $this->read_at = Carbon::now();
        $this->status = 'read';
        $this->save();

        return $this;
    }

    /**
     * Quick factory helper: create and broadcast (you'll wire broadcasting in controller/event)
     */
    public static function createForConversation(int $conversationId, int $senderId, int $receiverId, string $text, string $status = 'sent'): self
    {
        return self::create([
            'conversation_id' => $conversationId,
            'sender_id'       => $senderId,
            'receiver_id'     => $receiverId,
            'message'         => $text,
            'status'          => $status,
        ]);
    }
}
