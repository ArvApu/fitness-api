<?php

namespace App\Models;

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
    use HasFactory, Owned;

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
     * The exercises that belong to the workout.
     */
    public function exercises(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, WorkoutExercise::class)->withPivot([
            'reps', 'sets', 'rest'
        ]);
    }

    /**
     * Days that this workout has assigned.
     */
    public function days(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Day::class, WorkoutDay::class);
    }

    /**
     * Get this workout's logs
     */
    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkoutLog::class, 'workout_id');
    }

    /**
     * @inheritdoc
     */
    protected function getUserIdColumn(): string
    {
        return 'author_id';
    }
}
