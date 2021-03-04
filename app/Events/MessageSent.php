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

    /**
     * @var string
     */
    private $channel;

    public function __construct(Message $message, string $channel)
    {
        $this->message = $message;
        $this->channel = $channel;
    }

    public function broadcastOn()
    {
        return [
            new Channel('user.channel.'.$this->channel), // TODO: use private channel
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
