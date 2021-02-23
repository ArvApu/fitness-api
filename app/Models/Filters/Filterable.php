<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Filterable
 * @method Builder filter(array $input = [], $filter = null)
 * @method Builder paginateFilter($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 * @method Builder simplePaginateFilter($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 * @method Builder whereLike($column, $value, $boolean = 'and')
 * @method Builder whereBeginsWith($column, $value, $boolean = 'and')
 * @method Builder whereEndsWith($column, $value, $boolean = 'and')
 * @package App\Models\Filters
 */
trait Filterable
{
    use \EloquentFilter\Filterable;
}
