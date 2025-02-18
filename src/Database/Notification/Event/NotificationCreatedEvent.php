<?php

namespace App\Database\Notification\Event;

use App\Database\Notification\Entity\Notification;

class NotificationCreatedEvent
{
    public function __construct(private readonly Notification $notification)
    {
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}
