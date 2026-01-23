<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentController extends Controller
{
    
    // User submits manual payment request
     
    public function submit(Request $request)
    {
        $user = Auth::user();

        // Validate input
        try {
            $validated = $request->validate([
                'plan_id'        => 'required|exists:plans,id',
                'payment_method' => 'required|string|max:100',
                'transaction_id' => [
                    'required',
                    'string',
                    'unique:payments,transaction_id,NULL,id,user_id,' . $user->id,
                ],
                'sender_name'    => 'nullable|string|max:100',
                'sender_phone'   => 'nullable|string|max:20',
                'screenshot'     => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB
            ]);
        } catch (ValidationException $e) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422));
        }

        $path = null;

        // Upload screenshot to S3
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            $folder = "payments/{$user->id}";

            try {
               // $path = $file->store($folder, 's3');
                $path = $file->store($folder, 'public');
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Screenshot upload failed.',
                    'error'   => $e->getMessage(),
                ], 500);
            }
        }

        // Wrap DB insert in transaction
        try {
            $payment = DB::transaction(function () use ($user, $validated, $path) {
                return Payment::create([
                    'user_id'        => $user->id,
                    'plan_id'        => $validated['plan_id'],
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => $validated['transaction_id'],
                    'sender_name'    => $validated['sender_name'] ?? null,
                    'sender_phone'   => $validated['sender_phone'] ?? null,
                    'screenshot_path'=> $path,
                    'status'         => Payment::STATUS_PENDING,
                ]);
            });
        } catch (\Throwable $e) {
            // Rollback S3 file if DB insert fails
            if ($path && Storage::disk('s3')->exists($path)) {
                try { Storage::disk('s3')->delete($path); } catch (\Throwable $_) {}
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment request creation failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        $payment->load('plan');

        return response()->json([
            'success' => true,
            'message' => 'Payment request submitted. Waiting for admin approval.',
            'data'    => [
                'id'              => $payment->id,
                'user_id'         => $payment->user_id,
                'plan'            => [
                    'id'          => $payment->plan->id,
                    'plan_name'   => $payment->plan->plan_name,
                    'description' => $payment->plan->description,
                ],
                'payment_method'  => $payment->payment_method,
                'transaction_id'  => $payment->transaction_id,
                'sender_name'     => $payment->sender_name,
                'sender_phone'    => $payment->sender_phone,
                'screenshot_url'  => $payment->screenshot_url ? Storage::disk('s3')->url($payment->screenshot_path) : null,
                'status'          => $payment->status,
                'created_at'      => $payment->created_at,
                'updated_at'      => $payment->updated_at,
            ]
        ], 201);
    }

    
    // List user's own payments
    
    public function userPayments()
    {
        $payments = Auth::user()
            ->payments()
            ->with('plan')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($payment) => [
                'id'              => $payment->id,
                'plan'            => [
                    'id'          => $payment->plan->id,
                    'plan_name'   => $payment->plan->plan_name,
                    'description' => $payment->plan->description,
                ],
                'payment_method'  => $payment->payment_method,
                'transaction_id'  => $payment->transaction_id,
                'sender_name'     => $payment->sender_name,
                'sender_phone'    => $payment->sender_phone,
                'screenshot_url'  => $payment->screenshot_path ? Storage::disk('s3')->url($payment->screenshot_path) : null,
                'status'          => $payment->status,
                'created_at'      => $payment->created_at,
                'updated_at'      => $payment->updated_at,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $payments,
        ]);
    }
}
