<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessagesRead extends Event implements ShouldBroadcast
{
    use InteractsWithSockets;

    /**
     * @var Message[]
     */
    public $messages;

    /**
     * @var string
     */
    private $channel;

    /**
     * MessagesRead constructor.
     * @param Message[] $messages
     * @param string $channel
     */
    public function __construct(array $messages, string $channel)
    {
        $this->messages = $messages;
        $this->channel = $channel;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('user.' . $this->channel),
        ];
    }

    public function broadcastAs()
    {
        return 'read.messages';
    }
}
