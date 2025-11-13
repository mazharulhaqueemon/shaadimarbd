<?php

// namespace App\Events;

// use App\Models\Message;
// use Illuminate\Broadcasting\Channel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Broadcasting\PrivateChannel;


// class MessageSent implements ShouldBroadcast
// {
//     use InteractsWithSockets, SerializesModels;

//     public $message;

//     public function __construct(Message $message)
//     {
//         $this->message = $message->load(['sender:id,name', 'receiver:id,name']);
//     }

//     public function broadcastOn()
//     {
//         \Log::info('Broadcasting to channel: chat.' . $this->message->receiver_id);
//         return new PrivateChannel('chat.' . $this->message->receiver_id);
//     }

//     public function broadcastAs()
//     {
//         return 'message.sent';
//     }
// }


namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender:id,name', 'receiver:id,name']);
        Log::info('📨 MessageSent event instantiated', [
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'content' => $this->message->content ?? '[no content field]',
        ]);
    }

    public function broadcastOn()
    {
        $channelName = 'chat.' . $this->message->receiver_id;
        Log::info('🚀 Broadcasting MessageSent to channel', [
            'channel' => $channelName,
            'event' => 'message.sent',
        ]);
        return new PrivateChannel($channelName);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        $payload = [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at,
            'sender' => $this->message->sender,
            'receiver' => $this->message->receiver,
        ];
        Log::info('📦 Broadcast payload prepared', $payload);
        return $payload;
    }
}
