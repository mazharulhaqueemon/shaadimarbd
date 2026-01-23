<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get the authenticated user's full profile info
     */
    public function me(Request $request)
    {
        $user = Auth::user()->load('plan', 'profilePictures');

        // Get primary profile picture.  
        $primaryPicture = $user->profilePictures()->where('is_primary', true)->first();

        // Log primary picture info
        if ($primaryPicture) {
            $url = Storage::disk('public')->url($primaryPicture->image_path);
            Log::info('Primary profile picture', [
                'user_id' => $user->id,
                'picture_id' => $primaryPicture->id,
                'path' => $primaryPicture->image_path,
                'url' => $url,
            ]);
        } else {
            Log::info('No primary profile picture found', ['user_id' => $user->id]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'profile_id' => $user->profile->id ?? null,
                'plan' => [
                    'plan_name' => $user->plan->plan_name ?? null,
                    'description' => $user->plan->description ?? null,
                    'profile_picture_limit' => $user->plan->profile_picture_limit ?? null,
                    'phone_request_limit' => $user->plan->phone_request_limit ?? null,
                    'chat_duration_days' => $user->plan->chat_duration_days ?? null,
                ],
                'profile_picture' => $primaryPicture 
                    ? Storage::disk('public')->url($primaryPicture->image_path)
                    : null,
                'account_created_by' => $user->account_created_by,
                'plan_activated_at' => $user->plan_activated_at,
                'is_admin' => (bool) $user->is_admin,
            ],
        ]);
    }


    public function fullProfile( $id)
    {
        $user = User::with([
            'profile.education',
            'profile.career',
            'profile.familyDetail',
            'profile.location',
            'profile.lifestyle',
            'profile.partnerPreference',
            'profile.photos',
        ])->find($id);

        if (!$user || !$user->profile) {
            return response()->json([
                'success' => false,
                'message' => 'User or Profile not found',
                'data' => null,
            ], 404);
        }

        $profile = $user->profile;

        $response = [
            'id' => $profile->id,
            'user_id' => $user->id,
            'gender' => $profile->gender,
            'dob' => $profile->dob,
            'religion' => $profile->religion,
            'bio' => $profile->bio,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'education' => $profile->education ?? null,
            'career' => $profile->career ?? null,
            'family_detail' => $profile->familyDetail ?? null,
            'location' => $profile->location ?? null,
            'lifestyle' => $profile->lifestyle ?? null,
            'partner_preference' => $profile->partnerPreference ?? null,
            'photos' => $profile->photos ?? [],
        ];

        return response()->json([
            'success' => true,
            'message' => 'User full profile retrieved successfully',
            'data' => $response,
        ]);
    }

}
