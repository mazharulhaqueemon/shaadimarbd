<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ProfileProgressController extends Controller
{
    // ✅ Get current progress
    public function getProgress()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'profile_completion' => $profile->profile_completion,
            'last_completed_step' => $profile->last_completed_step,
        ]);
    }

    // ✅ Update progress after completing a step
    public function updateProgress(Request $request)
    {
        $request->validate([
            'completed_step' => 'required|integer|min:1|max:8',
        ]);

        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        // Each step = 12.5%
        $percentage = $request->completed_step * 12.5;

        $profile->update([
            'profile_completion' => $percentage,
            'last_completed_step' => $request->completed_step,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile progress updated successfully',
            'profile_completion' => $profile->profile_completion,
            'last_completed_step' => $profile->last_completed_step,
        ]);
    }
}
