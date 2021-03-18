<?php

namespace App\Models;

use App\Models\Traits\Owned;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Message
 * @property integer $id
 * @property integer $admin_id
 * @property string $name
 * @property User[] $users
 * @package App\Models
 */
class Room extends Model
{
    use HasFactory, Owned;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'name', 'admin_id'
    ];

    /**
     * Get all users in this room.
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, RoomUser::class);
    }

    /**
     * Get all room messages.
     */
    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'room_id');
    }

    /**
     * Get this room admin user.
     */
    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * @inheritdoc
     */
    protected function getUserIdColumn(): string
    {
        return 'admin_id';
    }
}
