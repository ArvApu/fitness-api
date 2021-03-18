<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class RoomUser
 * @property integer $room_id
 * @property integer $user_id
 * @mixin Builder
 * @package App\Models
 */
class RoomUser extends Pivot
{
    //
}
