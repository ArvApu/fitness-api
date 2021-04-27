<?php

namespace App\Listeners;

use App\Events\UserAcceptedInvitation;

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
