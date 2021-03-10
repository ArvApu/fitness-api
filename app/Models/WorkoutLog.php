<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class WorkoutLog
 * @property integer $id
 * @property integer $user_id
 * @property integer $workout_id
 * @property string $status
 * @property string $comment
 * @property string $difficulty
 * @property $created_at
 * @property $updated_at
 * @package App\Models
 */
class WorkoutLog extends Model
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'workout_id', 'status', 'comment', 'difficulty'
    ];

    /**
     * Get user of this log
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get workout of this log
     */
    public function workout(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Workout::class, 'workout_id');
    }
}
