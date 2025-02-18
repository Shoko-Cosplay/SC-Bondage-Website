<?php

namespace App\Database\Auth\Event;

use App\Database\Auth\User;

class BadPasswordLoginEvent
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
