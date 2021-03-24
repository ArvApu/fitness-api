<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{
    /**
     * @inheritdoc
     */
    public $relations = [];

    /**
     * @param string $query
     * @return UserFilter
     */
    public function q(string $query): UserFilter
    {
        return $this->where(function($q) use ($query) {
            return $q->where('first_name', 'LIKE', "%$query%")->orWhere('last_name', 'LIKE', "%$query%");
        });
    }
}
