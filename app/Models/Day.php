<?php

namespace App\Models;

/**
 * Class Day
 * @property integer $id
 * @property string $title
 * @property string $information
 * @property $date
 * @package App\Models
 */
class Day extends Model
{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * The workouts that belong to the day.
     */
    public function workouts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Workout::class, WorkoutDay::class);
    }
}
