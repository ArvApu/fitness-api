<?php

namespace App\Models;

use App\Models\Filters\Filterable;
use App\Models\Traits\Owned;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Workout
 * @property integer $id
 * @property integer $author_id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property boolean $is_private
 * @property $created_at
 * @property $updated_at
 * @package App\Models
 */
class Workout extends Model
{
    use HasFactory, Filterable, Owned;

    const MAX_NUMBER_OF_EXERCISES_ASSIGNED = 10;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'author_id', 'name', 'description', 'type', 'is_private'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'is_private' => 'boolean',
    ];

    /**
     * Get the author of this workout.
     */
    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Days that this workout has assigned.
     */
    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Event::class, 'workout_id');
    }

    /**
     * Get this workout's logs
     */
    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkoutLog::class, 'workout_id');
    }

    /**
     * Copy a workout with his assigned exercises list
     */
    public function copy(): Workout
    {
        $copy = $this->replicate();

        $copy->name = strlen($copy->name) > 90 ? $copy->id . ' (copy)' : $copy->name . ' (copy)';

        /* Saving model before recreation of relations so it would have an id */
        $copy->push();

        $keyed = $this->exercises()->get()->mapWithKeys(function ($exercise) {
            $data = $exercise->pivot->toArray();
            unset($data['workout_id']);
            return [take($data, 'exercise_id') => $data];
        });

        $copy->exercises()->attach($keyed);

        return $copy;
    }

    /**
     * The exercises that belong to the workout.
     */
    public function exercises(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, WorkoutExercise::class)->withPivot([
            'order', 'reps', 'sets', 'rest'
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getUserIdColumn(): string
    {
        return 'author_id';
    }
}
