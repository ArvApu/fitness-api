<?php

namespace App\Models;

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
    /**
     * Get the author of this exercise.
     */
    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
