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
     * Scope a query to only include user's models (possessions).
     *
     * @param Builder $query
     * @param int|null $userId
     * @return Builder
     */
    public function scopeOwnedBy(Builder $query, ?int $userId = null): Builder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $query;
        }

        $column = $this->getUserIdColumn();
        $userId = is_null($userId) ? $user->id : $userId;

        return $query->where($column, '=', $userId);
    }

    /**
     * @return string
     */
    protected function getUserIdColumn(): string
    {
        return 'user_id';
    }
}
