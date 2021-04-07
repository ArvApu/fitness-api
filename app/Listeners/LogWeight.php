<?php

namespace App\Listeners;

use App\Events\UserProfileUpdated;
use App\Models\UserLog;
use Carbon\Carbon;

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

        if (!$event->wasWeightUpdated()) {
            return;
        }

        $log = $user->logs()->whereDate('created_at', '=', Carbon::now())->first();

        if($log === null) {
            $user->logs()->create([
                'weight' => $user->weight
            ]);
        } else {
            $log->update([
                'weight' => $user->weight
            ]);
        }

    }

}
