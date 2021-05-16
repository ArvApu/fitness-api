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
 * @property $log_date
 * @property $created_at
 * @property $updated_at
 * @property User $user
 * @property Workout $workout
 * @package App\Models
 */
class WorkoutLog extends Model
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'workout_id', 'status', 'comment', 'difficulty', 'log_date'
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

    /**
     * Get workout of this log
     */
    public function exerciseLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExerciseLog::class, 'workout_log_id');
    }
}
