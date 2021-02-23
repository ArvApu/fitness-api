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
     * @param string $name
     * @return ExerciseFilter
     */
    public function name(string $name): ExerciseFilter
    {
        return $this->where('name', '=', $name);
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
