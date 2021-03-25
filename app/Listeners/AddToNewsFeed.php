<?php

namespace App\Listeners;

use App\Events\Event;
use App\Events\UserProfileUpdated;
use App\Models\NewsEvent;

class AddToNewsFeed
{
    /**
     * @var NewsEvent
     */
    private $news;

    /**
     * Create the event listener.
     *
     * @param NewsEvent $news
     */
    public function __construct(NewsEvent $news)
    {
        $this->news = $news;
    }

    /**
     * Handle the event.
     *
     * @param Event $event
     * @return void
     */
    public function handle(Event $event)
    {
        if ($event instanceof UserProfileUpdated) {
           $this->handleUserProfileUpdated($event);
        }
    }

    private function handleUserProfileUpdated(UserProfileUpdated $event): void
    {
        $user = $event->getUpdatedUser();
        $old  = $event->getOldUSer();

        if(!$user->isUser() || $user->trainer_id === null) {  // We are only interested what trained clients do
            return;
        }

        if($event->wasWeightUpdated()) {
            $this->news->create([
                'content' => "$user->full_name updated his weight from $old->weight to $user->weight",
                'user_id' => $user->trainer_id, // Because we want to inform this person's trainer
            ]);
        }

        if($event->wasNameChanged()) {
            $this->news->create([
                'content' => "$old->full_name change his name to $user->full_name",
                'user_id' => $user->trainer_id,
            ]);
        }
    }
}
