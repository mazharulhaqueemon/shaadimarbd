<?php

namespace App\Http\Controllers\API;

use App\Events\PhoneRequestResponded;
use App\Events\PhoneRequestSent;
use App\Http\Controllers\Controller;
use App\Models\PhoneRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneRequestController extends Controller
{
    
    // Send a phone number request
    public function sendRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $receiverId = $request->receiver_id;
        $sender = Auth::user();

        if ($receiverId == $sender->id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot send a request to yourself',
            ], 400);
        }

        // Check for existing pending request
        $existing = PhoneRequest::where('requester_id', $sender->id)
            ->where('receiver_id', $receiverId)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'Request already sent and is pending',
            ], 409);
        }

        // Plan-based limit check (only pending + accepted count)
        $requestsSent = PhoneRequest::where('requester_id', $sender->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();

        if ($requestsSent >= $sender->plan->phone_request_limit) {
            return response()->json([
                'status' => false,
                'message' => 'You have reached your phone request limit for your plan.',
            ], 403);
        }

        // Create new phone request
        $phoneRequest = PhoneRequest::create([
            'requester_id' => $sender->id,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        // Fire event immediately
        event(new PhoneRequestSent($phoneRequest));

        return response()->json([
            'status' => true,
            'message' => 'Phone request sent successfully',
            'data' => [
                'id' => $phoneRequest->id,
                'status' => $phoneRequest->status,
                'created_at' => $phoneRequest->created_at,
                'updated_at' => $phoneRequest->updated_at,
                'receiver' => [
                    'id' => $phoneRequest->receiver->id,
                    'name' => $phoneRequest->receiver->name,
                    'email' => $phoneRequest->receiver->email,
                ],
            ],
        ], 201);
    }

    
    // Accept or reject a request 
    public function respondRequest(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        $phoneRequest = PhoneRequest::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->first();

        if (!$phoneRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Request not found or unauthorized',
            ], 404);
        }

        $phoneRequest->status = $request->action === 'accept' ? 'accepted' : 'rejected';
        $phoneRequest->save();

        // Fire event immediately
        event(new PhoneRequestResponded($phoneRequest));

        // Optional: freed-up slot for rejected requests is automatic
        // Because in sendRequest we count only pending + accepted requests

        return response()->json([
            'status' => true,
            'message' => 'Request ' . $phoneRequest->status,
            'data' => [
                'id' => $phoneRequest->id,
                'status' => $phoneRequest->status,
                'created_at' => $phoneRequest->created_at,
                'updated_at' => $phoneRequest->updated_at,
                'requester' => [
                    'id' => $phoneRequest->requester->id,
                    'name' => $phoneRequest->requester->name,
                    'email' => $phoneRequest->requester->email,
                    'phone_number' => $phoneRequest->status === 'accepted'
                        ? $phoneRequest->requester->phone_number
                        : null,
                ],
            ],
        ], 200);
    }

    
    // List requests for logged-in user (requests they received)
     
    public function listRequests(Request $request)
    {
        $userId = Auth::id();

        $requests = PhoneRequest::where('receiver_id', $userId)
            ->with('requester')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'status' => $request->status,
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at,
                    'requester' => [
                        'id' => $request->requester->id,
                        'name' => $request->requester->name,
                        'email' => $request->requester->email,
                        'phone_number' => $request->status === 'accepted'
                            ? $request->requester->phone_number
                            : null,
                    ],
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $requests,
        ]);
    }
}
