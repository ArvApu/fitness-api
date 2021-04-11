<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RemindEvent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Event
     */
    protected $event;

    /**
     * RemindEvent constructor.
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): RemindEvent
    {
        return $this->subject('Upcoming event!')
            ->view('emails.remind_event')
            ->with([
                'title' => $this->event->title,
                'url' => ui_url('/client/calendar/events/'.$this->event->id)
            ]);
    }
}
