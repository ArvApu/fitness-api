<?php

namespace App\Listeners;

use App\Events\Event;
use App\Events\UserAcceptedInvitation;
use App\Events\UserProfileUpdated;
use App\Events\WorkoutLogged;
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
        } elseif ($event instanceof WorkoutLogged) {
            $this->handleWorkoutLogged($event);
        } elseif($event instanceof UserAcceptedInvitation) {
            $this->handleUserAcceptedInvitation($event);
        }
    }

    /**
     * @param UserProfileUpdated $event
     */
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

    /**
     * @param UserAcceptedInvitation $event
     */
    private function handleUserAcceptedInvitation(UserAcceptedInvitation $event): void
    {
        $user = $event->getUser();

        $this->news->create([
            'content' => "$user->full_name accepted your invitation.",
            'user_id' => $user->trainer_id,
        ]);
    }

    /**
     * @param WorkoutLogged $event
     */
    private function handleWorkoutLogged(WorkoutLogged $event)
    {
        $log     = $event->getLog();
        $content = $log->user->full_name;

        if(!$log->user->isUser() || $log->user->trainer_id === null) {
            return;
        }

        switch ($log->status) {
            case 'missed':
                $content .= " missed a '{$log->workout->name}' workout";
                break;
            case 'interrupted':
                $content .= " did not finish a '{$log->workout->name}'  workout";
                break;
            case 'completed':
                $content .= " completed a '{$log->workout->name}'  workout and marked it as {$log->difficulty}";
                break;
        }

        $this->news->create([
            'content' => $content,
            'user_id' => $log->user->trainer_id,
        ]);
    }
}
