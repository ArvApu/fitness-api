<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent extends Event implements ShouldBroadcast
{
    use InteractsWithSockets;

    /**
     * @var Message
     */
    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return [
            new Channel('testing'), // TODO: use private channel
        ];
    }

    public function broadcastAs()
    {
        return 'message-sent';
    }
}
