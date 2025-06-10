<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $loanId;
    public $status;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $loanId, $message, $status)
    {
        $this->userId = $userId;
        $this->loanId = $loanId;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('user.' . $this->userId)];
    }

    public function broadcastAs()
    {
        return 'payment.status.updated';
    }
}
