<?php

namespace App\Models;

use App\Models\Filters\Filterable;
use App\Models\Traits\Owned;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Exercise
 * @property integer $id
 * @property integer $author_id
 * @property string $name
 * @property string $url
 * @property string $description
 * @property string $measurement
 * @property $created_at
 * @property $updated_at
 * @package App\Models
 */
class Exercise extends Model
{
    use HasFactory, Filterable, Owned;

    /**
     * @inheritdoc
     */
    protected $appends = ['url_embedded'];

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'author_id', 'name', 'description', 'url', 'measurement'
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

    /**
     * @return string|null
     */
    public function getUrlEmbeddedAttribute(): ?string
    {
        if(is_null($this->url)) {
            return null;
        }

        return get_yt_embed_url($this->url);
    }

    /**
     * @inheritdoc
     */
    protected function getUserIdColumn(): string
    {
        return 'author_id';
    }
}
