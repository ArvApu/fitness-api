<?php

namespace App\Listeners;

use App\Events\UserProfileUpdated;
use App\Models\UserLog;

class LogWeight
{
    /**
     * @var UserLog
     */
    private $log;

    /**
     * Create the event listener.
     *
     * @param UserLog $log
     */
    public function __construct(UserLog $log)
    {
        $this->log = $log;
    }

    /**
     * Handle the event.
     *
     * @param UserProfileUpdated $event
     * @return void
     */
    public function handle(UserProfileUpdated $event)
    {
        $user = $event->getUpdatedUser();

        if($event->wasWeightUpdated()) {
            $user->logs()->create([
                'weight' => $user->weight
            ]);
        }
    }
}
