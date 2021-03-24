<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class WorkoutFilter extends ModelFilter
{
    /**
     * @param string $query
     * @return WorkoutFilter
     */
    public function q(string $query): WorkoutFilter
    {
        return $this->orWhere('name', 'like', "%$query%");
    }
}
