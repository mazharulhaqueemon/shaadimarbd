<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class FirebaseController extends Controller
{
    protected $auth;
    public function __construct(FirebaseAuth $auth){
        $this->auth = $auth;
    }

    public function verifyFirebaseToken(Request $request)
    {
        $customToken = $request->bearerToken(); // send token in Authorization: Bearer <token> header

        if (!$customToken) {
            return response()->json([
                'status' => false,
                'message' => 'No Firebase token provided'
            ], 400);
        }

        try {
            $verifiedToken = $this->auth->verifyIdToken($customToken);
            $uid = $verifiedToken->claims()->get('sub');
            $firebaseUser = $this->auth->getUser($uid);

            return response()->json([
                'status' => true,
                'message' => 'Firebase token is valid',
                'firebase_user' => $firebaseUser
            ], 200);

        } catch (FailedToVerifyToken $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Firebase token',
                'error' => $e->getMessage()
            ], 401);
        }
    }
}
