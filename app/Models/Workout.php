<?php

namespace App\Models;

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
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'author_id', 'name', 'description', 'type', 'is_private'
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
        return $this->belongsToMany(Exercise::class, WorkoutExercise::class);
    }

    /**
     * Days that this workout has assigned.
     */
    public function days(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Day::class, WorkoutDay::class);
    }
}
