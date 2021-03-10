<?php

namespace App\Models;

use App\Models\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Exercise
 * @property integer $id
 * @property integer $author_id
 * @property string $name
 * @property string $description
 * @property boolean $is_private
 * @property $created_at
 * @property $updated_at
 * @package App\Models
 */
class Exercise extends Model
{
    use HasFactory, Filterable;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'author_id', 'name', 'description', 'is_private'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'is_private' => 'boolean',
    ];

    /**
     * Get the author of this exercise.
     */
    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * The workouts that belong to the exercise.
     */
    public function workouts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Workout::class, WorkoutExercise::class);
    }

    /**
     * Get this workout's logs
     */
    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExerciseLog::class, 'exercise_id');
    }
}
