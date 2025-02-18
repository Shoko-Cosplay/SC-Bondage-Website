<?php

namespace App\Database\Auth\Subscriber;

use App\Database\Auth\Repository\LoginAttemptRepository;
use App\Database\Password\Event\PasswordRecoveredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordResetSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoginAttemptRepository $repository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PasswordRecoveredEvent::class => 'onPasswordRecovered',
        ];
    }

    public function onPasswordRecovered(PasswordRecoveredEvent $event): void
    {
        $this->repository->deleteAttemptsFor($event->getUser());
    }
}
