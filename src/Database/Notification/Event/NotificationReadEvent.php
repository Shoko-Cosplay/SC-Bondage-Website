<?php

namespace App\Database\Notification\Event;

use App\Database\Auth\User;

class NotificationReadEvent
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
