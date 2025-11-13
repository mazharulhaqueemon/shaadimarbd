<?php

use Illuminate\Support\Facades\Broadcast;

// Optional user-specific channel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat-specific channel
Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});



// use Illuminate\Support\Facades\Broadcast;
// use App\Models\Conversation;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     // Standard user channel authorization (no change needed here)
//     if (!$user) {
//         return false;
//     }
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    
//     // Check if $user is authenticated (Crucial for preventing 500)
//     if (!$user) {
//         return false;
//     }
//     // in the requested conversation ($conversationId).
//     return Conversation::where('id', $conversationId)
//         ->where(function ($q) use ($user) {
//             $q->where('user_one_id', $user->id) 
//               ->orWhere('user_two_id', $user->id);
//         })
//         ->exists();
// });