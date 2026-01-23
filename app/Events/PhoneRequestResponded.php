<?php

namespace App\Events;

use App\Models\PhoneRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhoneRequestResponded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $phoneRequest;

    public function __construct(PhoneRequest $phoneRequest)
    {
        $this->phoneRequest = $phoneRequest;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->phoneRequest->requester_id);
    }

    public function broadcastAs()
    {
        return 'phone-request.responded';
    }
}
