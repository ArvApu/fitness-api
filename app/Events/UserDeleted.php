<?php

namespace App\Events;

use App\Models\User;

class UserDeleted extends Event
{
    /**
     * @var User
     */
    protected $user;

    /**
     * UserDeleted constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
