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
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        Log::info('🎯 [MessageSent Event] Constructor called', [
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'has_relationships_loaded' => isset($message->relations),
        ]);

        $this->message = $message;
        
        Log::info('✅ [MessageSent Event] Message assigned successfully', [
            'message_id' => $this->message->id,
            'class' => get_class($this->message)
        ]);
    }

    public function broadcastOn()
    {
        Log::info('📡 [MessageSent Event] Preparing broadcast channels', [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id
        ]);

        $channels = [
            new PrivateChannel('chat.' . $this->message->receiver_id),
            new PrivateChannel('chat.' . $this->message->sender_id), 
        ];

        $channelNames = array_map(function($channel) {
            return $channel->name;
        }, $channels);

        Log::info('🚀 [MessageSent Event] Broadcasting to channels', [
            'channels' => $channelNames,
            'total_channels' => count($channels),
            'event_name' => 'message.sent'
        ]);

        return $channels;
    }

    public function broadcastAs()
    {
        Log::info('🏷️ [MessageSent Event] Setting broadcast event name', [
            'event_name' => 'message.sent'
        ]);
        return 'message.sent';
    }

    public function broadcastWith()
    {
        Log::info('📦 [MessageSent Event] Preparing broadcast payload', [
            'message_id' => $this->message->id,
            'step' => 'starting_payload_preparation'
        ]);

        try {
            // Load relationships safely for broadcasting
            if (!isset($this->message->relations['sender']) || !isset($this->message->relations['receiver'])) {
                Log::info('🔄 [MessageSent Event] Loading relationships for message', [
                    'message_id' => $this->message->id
                ]);
                $this->message->load([
                    'sender:id,name,email',
                    'receiver:id,name,email'
                ]);
            }

            $payload = [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->toISOString(),
                'conversation_id' => $this->message->conversation_id,
                'sender' => [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                    'email' => $this->message->sender->email,
                ],
                'receiver' => [
                    'id' => $this->message->receiver->id,
                    'name' => $this->message->receiver->name,
                    'email' => $this->message->receiver->email,
                ]
            ];

            Log::info('✅ [MessageSent Event] Payload prepared successfully', [
                'message_id' => $this->message->id,
                'payload_keys' => array_keys($payload),
                'has_sender' => !empty($payload['sender']),
                'has_receiver' => !empty($payload['receiver'])
            ]);

            return $payload;

        } catch (\Exception $e) {
            Log::error('❌ [MessageSent Event] Error preparing broadcast payload', [
                'message_id' => $this->message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback payload without relationships
            return [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->toISOString(),
                'conversation_id' => $this->message->conversation_id,
                'error' => 'Failed to load user relationships'
            ];
        }
    }

    public function __destruct()
    {
        Log::info('🧹 [MessageSent Event] Event instance destroyed', [
            'message_id' => $this->message->id ?? 'unknown'
        ]);
    }
}