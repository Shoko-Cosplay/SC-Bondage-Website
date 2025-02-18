<?php

namespace App\Database\Password\Event;

use App\Database\Auth\User;

final readonly class PasswordRecoveredEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
