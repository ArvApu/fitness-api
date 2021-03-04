<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendMessage extends Event implements ShouldBroadcast
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
            new PrivateChannel('user.'.$this->channel),
        ];
    }

    public function broadcastAs()
    {
        return 'send.message';
    }
}
