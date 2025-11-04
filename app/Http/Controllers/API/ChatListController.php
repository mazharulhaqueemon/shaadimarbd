<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatListController extends Controller
{
    // GET /api/chat/list
    public function index()
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'data' => [],
                ], 401);
            }

            // 1️⃣ Auto-populate chat_list from chat_logs if missing
            $existingChats = ChatList::where('user_id', $userId)
                ->pluck('chat_id')
                ->toArray();

            $chatLogs = DB::table('chat_logs')
                ->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)
                        ->orWhere('receiver_id', $userId);
                })
                ->whereNotIn('chat_id', $existingChats)
                ->get();

            $insertData = [];
            foreach ($chatLogs as $log) {
                $otherUserId = ($log->sender_id === $userId) ? $log->receiver_id : $log->sender_id;

                $insertData[] = [
                    'user_id' => $userId,
                    'other_user_id' => $otherUserId,
                    'chat_id' => $log->chat_id,
                    'last_message' => $log->message,
                    'last_message_at' => $log->created_at,
                    'unread_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($insertData)) {
                ChatList::insert($insertData);
            }

            // 2️⃣ Fetch chat list with related user and primary profile picture
            $chatList = ChatList::with(['otherUser.primaryProfilePicture'])
                ->where('user_id', $userId)
                ->orderBy('last_message_at', 'desc')
                ->get()
                ->map(function ($chat) {
                    $otherUser = $chat->otherUser;

                    $primaryPicture = $otherUser?->primaryProfilePicture;
                    $avatar = $primaryPicture
                        ? asset('storage/' . $primaryPicture->image_path)
                        : 'https://cdn-icons-png.flaticon.com/512/847/847969.png';

                    return [
                        'id' => $chat->id,
                        'chat_id' => $chat->chat_id,
                        'other_user' => [
                            'id' => $otherUser->id ?? null,
                            'name' => $otherUser->name ?? 'Unknown',
                            'avatar' => $avatar,
                        ],
                        'last_message' => $chat->last_message,
                        'last_message_at' => $chat->last_message_at,
                        'unread_count' => $chat->unread_count,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Chat list retrieved successfully',
                'data' => $chatList,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('ChatListController@index error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    // POST /api/chat/list
    public function store(Request $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $otherUserId = $request->input('other_user_id');
            if (!$otherUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'other_user_id is required',
                ], 400);
            }

            // Prevent duplicates
            $chat = ChatList::firstOrCreate(
                [
                    'user_id' => $userId,
                    'other_user_id' => $otherUserId,
                ],
                [
                    'chat_id' => $userId < $otherUserId
                        ? "{$userId}_{$otherUserId}"
                        : "{$otherUserId}_{$userId}",
                    'last_message' => '',
                    'last_message_at' => now(),
                    'unread_count' => 0,
                ]
            );

            $otherUser = $chat->otherUser()->with('primaryProfilePicture')->first();

            $primaryPicture = $otherUser?->primaryProfilePicture;
            $avatar = $primaryPicture
                ? asset('storage/' . $primaryPicture->image_path)
                : 'https://cdn-icons-png.flaticon.com/512/847/847969.png';

            return response()->json([
                'success' => true,
                'message' => 'Chat created/retrieved successfully',
                'data' => [
                    'id' => $chat->id,
                    'chat_id' => $chat->chat_id,
                    'other_user' => [
                        'id' => $otherUser->id ?? null,
                        'name' => $otherUser->name ?? 'Unknown',
                        'avatar' => $avatar,
                    ],
                    'last_message' => $chat->last_message,
                    'last_message_at' => $chat->last_message_at,
                    'unread_count' => $chat->unread_count,
                ],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('ChatListController@store error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
