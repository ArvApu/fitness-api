<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class ExerciseFilter extends ModelFilter
{
    /**
     * @inheritdoc
     */
    public $relations = [];

    /**
     * @param string $query
     * @return ExerciseFilter
     */
    public function q(string $query): ExerciseFilter
    {
        return $this->orWhere('name', 'like', "%$query%");
    }

    /**
     * @param int $author
     * @return ExerciseFilter
     */
    public function author(int $author): ExerciseFilter
    {
        return $this->where('author_id', '=', $author);
    }
}
