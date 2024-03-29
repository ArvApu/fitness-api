<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Day
 * @property integer $id
 * @property integer $organizer_id
 * @property integer $workout_id
 * @property string $title
 * @property string $information
 * @property boolean $all_day
 * @property $start_time
 * @property $end_time
 * @property $created_at
 * @property $updated_at
 * @property User $attendee
 * @package App\Models
 */
class Event extends Model
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'title', 'information', 'all_day', 'start_time', 'end_time', 'attendee_id', 'organizer_id', 'workout_id'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'start_time' => 'datetime:Y-m-d H:i:s',
        'end_time' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The workout that this event has been set to.
     */
    public function workout(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Workout::class, 'workout_id');
    }

    /**
     * The attendee of this event.
     */
    public function attendee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'attendee_id');
    }

    /**
     * The organizer of this event.
     */
    public function organizer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
}
