<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Model
 * @mixin Builder
 * @package App\Models
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
