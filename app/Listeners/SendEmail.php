<?php

namespace App\Listeners;

use App\Events\Event;
use App\Events\UserDeleted;
use App\Mail\DeleteUser;
use Illuminate\Contracts\Mail\Mailer;

class SendEmail
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Create the event listener.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param Event $event
     * @return void
     */
    public function handle(Event $event)
    {
        if ($event instanceof UserDeleted) {
            $this->handleUserDeleted($event);
        }
    }

    /**
     * @param UserDeleted $event
     */
    private function handleUserDeleted(UserDeleted $event): void
    {
        $this->mailer->to($event->getUser()->email)->send(new DeleteUser());
    }
}
