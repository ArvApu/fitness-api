<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class UserLog
 * @property integer $id
 * @property integer $user_id
 * @property double $weight
 * @property $log_date
 * @property $created_at
 * @package App\Models
 */
class UserLog extends Model
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
        'user_id', 'weight', 'log_date'
    ];

    /**
     * @inheritdoc
     */
    public static function booted()
    {
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    /**
     * Get log's user.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
