<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Message
 * @property integer $id
 * @property integer $user_id
 * @property integer $room_id
 * @property string $message
 * @property boolean $is_seen
 * @property $created_at
 * @package App\Models
 */
class Message extends Model
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'room_id', 'message', 'is_seen'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'is_seen' => 'boolean',
    ];

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    /**
     * Get user that sent this message.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get room where this messages is.
     */
    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
