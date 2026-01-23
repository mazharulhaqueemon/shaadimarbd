<?php

namespace App\Http\Controllers\API;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;

class AuthController extends Controller
{
    
    // Register a new user
    
    public function signup(SignupRequest $request)
{
    // Fetch the existing 'Basics' plan
    $basicPlan = Plan::where('plan_name', 'Basics')->first();

    if (!$basicPlan) {
        return response()->json([
            'status' => false,
            'message' => 'Default plan not found. Please contact support.',
        ], 500);
    }

    // Create the user and assign the Basics plan
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password), // Always hash passwords
        'account_created_by' => $request->account_created_by,
        'phone_number' => $request->phone_number ?? null,
        'plan_id' => $basicPlan->id,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'User created successfully',
        'data' => $user,
    ], 201);
}


    
    // Authenticate user and generate Laravel Sanctum token

    public function login(LoginRequest $request){
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $user = Auth::user();

        // Make sure the user has a profile relationship
        $profileId = $user->profile?->id ?? null;

        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'profile_id' => $profileId, // ✅ include profile ID
                'plan' => $user->plan,
                'account_created_by' => $user->account_created_by,
            ],
            'token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ], 200);
    }

    return response()->json([
        'status' => false,
        'message' => 'Authentication Failed',
    ], 401);
}


    
    // Logout and revoke tokens
    
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully',
        ], 200);
    }
}
