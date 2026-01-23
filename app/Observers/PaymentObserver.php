<?php

namespace App\Observers;
use App\Models\Payment;

class PaymentObserver
{
    
    // Handle the Payment "updated" event.
    public function updated(Payment $payment): void
    {
        // Check if status changed to approved
        if ($payment->isDirty('status') && $payment->status === Payment::STATUS_APPROVED) {
            $user = $payment->user;
            if ($user) {
                $user->plan_id = $payment->plan_id;
                $user->plan_activated_at = now();
                $user->save();
            }
        }
    }
}
