<?php

namespace App\Models;

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
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'author_id', 'name', 'description', 'is_private'
    ];

    /**
     * Get the author of this exercise.
     */
    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
