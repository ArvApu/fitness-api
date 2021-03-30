<?php

namespace App\Events;

use App\Models\WorkoutLog;

class WorkoutLogged extends Event
{
    /**
     * @var WorkoutLog
     */
    protected $log;

    /**
     * WorkoutLogged constructor.
     * @param WorkoutLog $log
     */
    public function __construct(WorkoutLog $log)
    {
        $this->log = $log;
    }

    /**
     * @return WorkoutLog
     */
    public function getLog(): WorkoutLog
    {
        return $this->log;
    }
}
