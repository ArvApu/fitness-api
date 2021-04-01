<?php

namespace App\Listeners;

use App\Events\Event;
use App\Events\UserAcceptedInvitation;
use App\Events\UserProfileUpdated;
use App\Events\WorkoutLogged;
use App\Models\NewsEvent;
use App\Models\Room;

class CreateRoom
{
    /**
     * Handle the event.
     *
     * @param UserAcceptedInvitation $event
     * @return void
     */
    public function handle(UserAcceptedInvitation $event)
    {
        $user = $event->getUser();

        if ($user->role !== 'user') {
            return;
        }

        $room = $user->trainer->administratedRooms()->create([
            'name' => $user->full_name,
        ]);

        $room->users()->attach([$user->id, $user->trainer->id]);
    }
}
