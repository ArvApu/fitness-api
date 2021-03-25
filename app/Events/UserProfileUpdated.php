<?php

namespace App\Events;

use App\Models\User;

class UserProfileUpdated extends Event
{
    /**
     * @var User
     */
    protected $updated;

    /**
     * @var User
     */
    protected $old;

    /**
     * UserProfileUpdated constructor.
     * @param User $updated
     * @param User $old
     */
    public function __construct(User $updated, User $old)
    {
        $this->updated = $updated;
        $this->old = $old;
    }

    /**
     * @return User
     */
    public function getUpdatedUser(): User
    {
        return $this->updated;
    }

    /**
     * @return User
     */
    public function getOldUser(): User
    {
        return $this->old;
    }

    /**
     * @return bool
     */
    public function wasWeightUpdated(): bool
    {
        return $this->getUpdatedUser()->weight !== $this->getOldUser()->weight;
    }

    /**
     * @return bool
     */
    public function wasNameChanged(): bool
    {
        return $this->getUpdatedUser()->full_name !== $this->getOldUser()->full_name;
    }
}
