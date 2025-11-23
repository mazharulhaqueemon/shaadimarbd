<?php

// namespace App\Events;

// use App\Models\Message;
// use Illuminate\Broadcasting\Channel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Support\Facades\Log;

// class MessageSent implements ShouldBroadcast
// {
//     use InteractsWithSockets, SerializesModels;

//     public $message;

//     public function __construct(Message $message)
//     {
//         $this->message = $message->load(['sender:id,name', 'receiver:id,name']);
//         Log::info('📨 MessageSent event instantiated', [
//             'sender_id' => $this->message->sender_id,
//             'receiver_id' => $this->message->receiver_id,
//             'content' => $this->message->content ?? '[no content field]',
//         ]);
//     }

//     public function broadcastOn()
// {
//     $channels = [
//         new PrivateChannel('chat.' . $this->message->receiver_id),
//         new PrivateChannel('chat.' . $this->message->sender_id), 
//     ];
//     Log::info('🚀 Broadcasting MessageSent to channels', [
//         'channels' => $channels,
//         'event' => 'message.sent',
//     ]);
//     return $channels;
// }


//     public function broadcastAs()
//     {
//         return 'message.sent';
//     }

//     public function broadcastWith()
//     {
//         $payload = [
//             'id' => $this->message->id,
//             'sender_id' => $this->message->sender_id,
//             'receiver_id' => $this->message->receiver_id,
//             'message' => $this->message->message,
//             'created_at' => $this->message->created_at,
//             'sender' => $this->message->sender,
//             'receiver' => $this->message->receiver,
//         ];
//         Log::info('📦 Broadcast payload prepared', $payload);
//         return $payload;
//     }
// }




namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets; // Removed SerializesModels

    public $message;

    public function __construct(Message $message)
    {
        // Don't load relationships in constructor to avoid serialization issues
        $this->message = $message;
        
        Log::info('📨 [MessageSent Event] Constructor - No queue used', [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
        ]);
    }

    public function broadcastOn()
    {
        $channels = [
            new PrivateChannel('chat.' . $this->message->receiver_id),
            new PrivateChannel('chat.' . $this->message->sender_id), 
        ];
        
        Log::info('🚀 [MessageSent Event] Broadcasting to channels', [
            'channels' => [
                'chat.' . $this->message->receiver_id,
                'chat.' . $this->message->sender_id
            ]
        ]);
        
        return $channels;
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        // Load relationships here when needed
        if (!isset($this->message->relations['sender']) || !isset($this->message->relations['receiver'])) {
            $this->message->load(['sender:id,name', 'receiver:id,name']);
        }

        $payload = [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at->toISOString(),
            'sender' => $this->message->sender,
            'receiver' => $this->message->receiver,
        ];
        
        Log::info('📦 [MessageSent Event] Payload ready', [
            'message_id' => $this->message->id
        ]);
        
        return $payload;
    }
}