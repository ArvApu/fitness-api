<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Message
 * @property integer $id
 * @property integer $sender_id
 * @property integer $receiver_id
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
        'sender_id', 'receiver_id', 'message', 'is_seen',
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
    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get user that should receive this message.
     */
    public function receiver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
