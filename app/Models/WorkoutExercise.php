<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class WorkoutExercise
 * @property integer $workout_id
 * @property integer $exercise_id
 * @property integer $reps
 * @property integer $sets
 * @property integer $rest
 * @mixin Builder
 * @package App\Models
 */
class WorkoutExercise extends Pivot
{
    //
}
