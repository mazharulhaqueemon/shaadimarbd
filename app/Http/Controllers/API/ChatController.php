<?php

// namespace App\Http\Controllers\API;
// use App\Http\Controllers\Controller;

// use App\Models\Conversation;
// use App\Models\Message;
// use App\Events\MessageSent;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;

// class ChatController extends Controller
// {
//     /**
//      * Get all conversations for the current user
//      */
// public function conversations()
// {
//     $userId = Auth::id();

//     $conversations = Conversation::where('user_one_id', $userId)
//         ->orWhere('user_two_id', $userId)
//         ->with([
//             'userOne:id,name', 
//             'userOne.primaryProfilePicture:id,user_id,image_path,is_primary',
//             'userTwo:id,name', 
//             'userTwo.primaryProfilePicture:id,user_id,image_path,is_primary'
//         ])
//         ->latest('updated_at')
//         ->get()
//         ->map(function ($conv) use ($userId) {
//             $otherUser = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;

//             $profilePic = $otherUser->primaryProfilePicture?->image_path
//                 ? asset('storage/' . $otherUser->primaryProfilePicture->image_path)
//                 : null;

//             return [
//                 'id' => $conv->id,
//                 'other_user' => [
//                     'id' => $otherUser->id,
//                     'name' => $otherUser->name,
//                     'profile_photo' => $profilePic,
//                 ],
//                 'last_message' => $conv->messages()->latest()->first()?->message ?? '',
//                 'updated_at' => $conv->updated_at,
//                 'current_user_id' => $userId, // ✅ Add current user ID here
//             ];
//         });

//     return response()->json($conversations);
// }




//     /**
//      * Get all messages for a conversation between the current user and another user
//      */
//     public function messages($otherUserId)
//     {
//         $userId = Auth::id();

//         $conversation = Conversation::between($userId, $otherUserId)->first();

//         if (!$conversation) {
//             return response()->json(['messages' => []]);
//         }

//         $messages = Message::where('conversation_id', $conversation->id)
//             ->orderBy('created_at', 'asc')
//             ->get();

//         // Optionally mark unread messages as read
//         Message::where('conversation_id', $conversation->id)
//             ->where('receiver_id', $userId)
//             ->whereNull('read_at')
//             ->update(['status' => 'read', 'read_at' => now()]);

//         return response()->json($messages);
//     }

//     /**
//      * Send a new message to another user
//      */
//     public function sendMessage(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'receiver_id' => 'required|integer|exists:users,id',
//             'message' => 'required|string|max:1000',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['errors' => $validator->errors()], 422);
//         }

//         $senderId = Auth::id();
//         $receiverId = $request->receiver_id;

//         // Ensure one consistent conversation per pair
//         $conversation = Conversation::getOrCreateBetween($senderId, $receiverId);
//         $conversation->touch(); // update updated_at

//         // Create message record
//         $message = Message::create([
//             'conversation_id' => $conversation->id,
//             'sender_id' => $senderId,
//             'receiver_id' => $receiverId,
//             'message' => $request->message,
//             'status' => 'sent',
//         ]);

        


//         // 🔥 Broadcast event (Reverb will handle this later)

//         broadcast(new MessageSent($message))->toOthers();
//         \Log::info('[Broadcast] MessageSent event dispatched', [
//             'message_id' => $message->id,
//             'receiver_id' => $receiverId,
//             'conversation_id' => $conversation->id,
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Message sent successfully.',
//             'data' => $message,
//         ]);
//     }
// }



namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Get all conversations for the current user
     */
    public function conversations()
    {
        Log::info('💬 [ChatController] Fetching conversations', [
            'user_id' => Auth::id()
        ]);

        $userId = Auth::id();

        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with([
                'userOne:id,name', 
                'userOne.primaryProfilePicture:id,user_id,image_path,is_primary',
                'userTwo:id,name', 
                'userTwo.primaryProfilePicture:id,user_id,image_path,is_primary'
            ])
            ->latest('updated_at')
            ->get()
            ->map(function ($conv) use ($userId) {
                $otherUser = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;

                $profilePic = $otherUser->primaryProfilePicture?->image_path
                    ? asset('storage/' . $otherUser->primaryProfilePicture->image_path)
                    : null;

                return [
                    'id' => $conv->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'profile_photo' => $profilePic,
                    ],
                    'last_message' => $conv->messages()->latest()->first()?->message ?? '',
                    'updated_at' => $conv->updated_at,
                    'current_user_id' => $userId,
                ];
            });

        Log::info('✅ [ChatController] Conversations fetched successfully', [
            'user_id' => $userId,
            'conversations_count' => $conversations->count()
        ]);

        return response()->json($conversations);
    }

    /**
     * Get all messages for a conversation between the current user and another user
     */
    public function messages($otherUserId)
    {
        Log::info('💬 [ChatController] Fetching messages', [
            'current_user_id' => Auth::id(),
            'other_user_id' => $otherUserId
        ]);

        $userId = Auth::id();

        $conversation = Conversation::between($userId, $otherUserId)->first();

        if (!$conversation) {
            Log::info('ℹ️ [ChatController] No conversation found', [
                'user_id' => $userId,
                'other_user_id' => $otherUserId
            ]);
            return response()->json(['messages' => []]);
        }

        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read
        $unreadUpdated = Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['status' => 'read', 'read_at' => now()]);

        if ($unreadUpdated > 0) {
            Log::info('📖 [ChatController] Marked messages as read', [
                'conversation_id' => $conversation->id,
                'unread_count' => $unreadUpdated
            ]);
        }

        Log::info('✅ [ChatController] Messages fetched successfully', [
            'conversation_id' => $conversation->id,
            'messages_count' => $messages->count(),
            'unread_updated' => $unreadUpdated
        ]);

        return response()->json($messages);
    }

    /**
     * Send a new message to another user
     */
    public function sendMessage(Request $request)
    {
        Log::info('✉️ [ChatController] sendMessage called', [
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message_length' => strlen($request->message)
        ]);

        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            Log::error('❌ [ChatController] Message validation failed', [
                'errors' => $validator->errors()->toArray(),
                'sender_id' => Auth::id()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;

        Log::info('🔍 [ChatController] Finding or creating conversation', [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId
        ]);

        // Ensure one consistent conversation per pair
        $conversation = Conversation::getOrCreateBetween($senderId, $receiverId);
        $conversation->touch(); // update updated_at

        Log::info('💾 [ChatController] Creating message record', [
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId
        ]);

        // Create message record
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'status' => 'sent',
        ]);

        Log::info('✅ [ChatController] Message created successfully', [
            'message_id' => $message->id,
            'conversation_id' => $conversation->id
        ]);

        // Load relationships before broadcasting
        Log::info('🔄 [ChatController] Loading message relationships', [
            'message_id' => $message->id
        ]);
        
        $message->load(['sender:id,name,email', 'receiver:id,name,email']);

        Log::info('📡 [ChatController] Broadcasting MessageSent event', [
            'message_id' => $message->id,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'channels' => ['chat.' . $receiverId, 'chat.' . $senderId]
        ]);

        try {
            // Broadcast the event
            broadcast(new MessageSent($message))->toOthers();
            
            Log::info('🎉 [ChatController] MessageSent event broadcasted successfully', [
                'message_id' => $message->id,
                'broadcast_status' => 'queued'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [ChatController] Failed to broadcast MessageSent event', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        Log::info('🏁 [ChatController] sendMessage completed successfully', [
            'message_id' => $message->id,
            'response_sent' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message,
        ]);
    }
}