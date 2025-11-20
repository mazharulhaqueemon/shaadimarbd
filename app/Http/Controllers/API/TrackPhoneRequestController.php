<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\TrackPhoneRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
class TrackPhoneRequestController extends Controller
{

public function store(Request $request)
{
    $user = Auth::user();
    if (! $user) {
        return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
    }

    $data = $request->validate([
        'viewed_user_id' => 'required|exists:users,id',
    ]);

    $viewerId = $user->id;
    $viewedUserId = (int) $data['viewed_user_id'];

    $phoneLimit = optional($user->plan)->phone_request_limit ?? 0;

    // Find or create row
    $tracker = TrackPhoneRequest::firstOrCreate(
        ['viewer_user_id' => $viewerId],
        ['viewed_user_ids' => [], 'count' => 0]
    );

    $viewed = $tracker->viewed_user_ids ?? [];

    // Already viewed
    if (in_array($viewedUserId, $viewed, true)) {
        return response()->json([
            'success' => true,
            'message' => 'Phone already viewed for this user.',
            'current_count' => $tracker->count,
            'limit' => $phoneLimit,
            'allowed' => true
        ]);
    }

    // Limit reached
    if ($tracker->count >= $phoneLimit) {
        return response()->json([
            'success' => false,
            'message' => 'Phone view limit reached.',
            'current_count' => $tracker->count,
            'limit' => $phoneLimit,
            'allowed' => false
        ], 403);
    }

    // Atomic DB transaction to prevent race conditions
    DB::transaction(function () use ($tracker, $viewedUserId, $viewed) {
        $viewed[] = $viewedUserId;

        $tracker->viewed_user_ids = array_values($viewed);
        $tracker->count = $tracker->count + 1;

        $tracker->save();
    });

    return response()->json([
        'success' => true,
        'message' => 'Phone view recorded.',
        'current_count' => $tracker->count,
        'limit' => $phoneLimit,
        'allowed' => true
    ]);
}

public function stats(Request $request)
{
    $user = Auth::user();
    if (! $user) {
        return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
    }

    // viewed_user_id = the profile we're checking (Sadia)
    $viewedUserId = (int) $request->query('viewed_user_id');
    if (! $viewedUserId) {
        return response()->json(['success' => false, 'message' => 'viewed_user_id is required'], 422);
    }

    // LIMIT is from the VIEWER's plan (Sakib)
    $phoneLimit = optional($user->plan)->phone_request_limit ?? 0;

    // Tracker row belongs to the viewer (one row per viewer)
    $tracker = TrackPhoneRequest::where('viewer_user_id', $user->id)->first();

    $currentCount = $tracker->count ?? 0;
    $viewedUsers = $tracker->viewed_user_ids ?? [];

    $alreadyViewed = in_array($viewedUserId, $viewedUsers, true);

    return response()->json([
        'success' => true,
        'already_viewed' => $alreadyViewed,
        'current_count' => $currentCount,
        'limit' => $phoneLimit,
        'remaining' => max(0, $phoneLimit - $currentCount),
        // allowed means viewer can view (either under limit OR already viewed this profile)
        'allowed' => ($currentCount < $phoneLimit) || $alreadyViewed,
    ]);
}
}