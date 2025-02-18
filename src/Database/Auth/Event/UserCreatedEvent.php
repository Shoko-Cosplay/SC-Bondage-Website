<?php

namespace App\Database\Auth\Event;

use App\Database\Auth\User;

class UserCreatedEvent
{
    public function __construct(private readonly User $user, private readonly bool $usingOauth = false)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isUsingOauth(): bool
    {
        return $this->usingOauth;
    }
}
