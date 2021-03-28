<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class WorkoutLog
 * @property integer $id
 * @property integer $user_id
 * @property integer $exercise_id
 * @property integer $workout_log_id
 * @property double $measurement_value
 * @property integer $sets_count
 * @property integer $sets_done
 * @property string $comment
 * @property $created_at
 * @property $updated_at
 * @package App\Models
 */
class ExerciseLog extends Model
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'exercise_id', 'workout_log_id', 'measurement_value', 'sets_count', 'sets_done', 'comment'
    ];

    /**
     * Get user of this log
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get exercise of this log
     */
    public function exercise(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    /**
     * Get workout of this log
     */
    public function workoutLog(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WorkoutLog::class, 'workout_log_id');
    }
}
