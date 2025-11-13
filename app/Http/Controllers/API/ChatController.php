<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Get all conversations for the current user
     */
public function conversations()
{
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
                'current_user_id' => $userId, // ✅ Add current user ID here
            ];
        });

    return response()->json($conversations);
}




    /**
     * Get all messages for a conversation between the current user and another user
     */
    public function messages($otherUserId)
    {
        $userId = Auth::id();

        $conversation = Conversation::between($userId, $otherUserId)->first();

        if (!$conversation) {
            return response()->json(['messages' => []]);
        }

        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Optionally mark unread messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['status' => 'read', 'read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Send a new message to another user
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;

        // Ensure one consistent conversation per pair
        $conversation = Conversation::getOrCreateBetween($senderId, $receiverId);
        $conversation->touch(); // update updated_at

        // Create message record
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'status' => 'sent',
        ]);

        


        // 🔥 Broadcast event (Reverb will handle this later)

        broadcast(new MessageSent($message))->toOthers();
        \Log::info('[Broadcast] MessageSent event dispatched', [
            'message_id' => $message->id,
            'receiver_id' => $receiverId,
            'conversation_id' => $conversation->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message,
        ]);
    }
}
