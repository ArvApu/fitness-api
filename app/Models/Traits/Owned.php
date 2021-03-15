<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait Owned
 * @package App\Models\Traits
 *
 * @method Builder ownedBy(?int $userId = null)
 * @method Builder owned()
 */
trait Owned
{
    /**
     * Scope a query to only include user's models (possessions).
     *
     * @param Builder $query
     * @param int|null $userId
     * @return Builder
     */
    public function scopeOwnedBy(Builder $query, ?int $userId = null): Builder
    {
        $column = $this->getUserIdColumn();
        $userId = is_null($userId) ? Auth::id() : $userId;
        return $query->where($column, '=', $userId);
    }

    /**
     * Alias method for owned by
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOwned(Builder $query): Builder
    {
        return $this->scopeOwnedBy($query);
    }

    /**
     * @return string
     */
    protected function getUserIdColumn(): string
    {
        return 'user_id';
    }
}
