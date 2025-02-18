<?php

namespace App\Database\Auth\Service;

use App\Database\Auth\Event\UserBannedEvent;
use App\Database\Auth\User;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserBanService
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public function ban(User $user): void
    {
        $user->setBannedAt(new \DateTimeImmutable());
        $this->dispatcher->dispatch(new UserBannedEvent($user));
    }
}
