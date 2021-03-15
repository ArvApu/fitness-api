<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Model
 * @mixin Builder
 * @package App\Models
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @inheritdoc
     */
    protected $perPage = 10;

    /**
     * @inheritdoc
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
