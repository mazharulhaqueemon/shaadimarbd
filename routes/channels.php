<?php
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat-specific channel
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    Log::info('🔐 Channel authorization check', [
        'authenticated_user_id' => $user->id,
        'requested_channel_user_id' => $userId,
        'authorized' => (int) $user->id === (int) $userId
    ]);
    
    return (int) $user->id === (int) $userId;
});