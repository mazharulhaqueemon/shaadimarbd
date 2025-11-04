<?php

// app/Models/ChatList.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatList extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'chat_list';

    protected $fillable = [
        'user_id',
        'other_user_id',
        'chat_id',
        'last_message',
        'last_message_at',
        'unread_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function otherUser()
    {
        return $this->belongsTo(User::class, 'other_user_id');
    }
}
