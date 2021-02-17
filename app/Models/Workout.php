<?php

namespace App\Models;

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
}
