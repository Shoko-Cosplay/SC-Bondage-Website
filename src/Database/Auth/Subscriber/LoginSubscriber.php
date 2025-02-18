<?php

namespace App\Database\Auth\Subscriber;

use App\Database\Auth\Event\BadPasswordLoginEvent;
use App\Database\Auth\Service\LoginAttemptService;
use App\Database\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoginAttemptService $service, private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BadPasswordLoginEvent::class => 'onAuthenticationFailure',
            LoginSuccessEvent::class => 'onLogin',
        ];
    }

    public function onAuthenticationFailure(BadPasswordLoginEvent $event): void
    {
        $this->service->addAttempt($event->getUser());
    }

    public function onLogin(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $event->getRequest()->getClientIp();
        if ($user instanceof User) {
            $ip = $event->getRequest()->getClientIp();
            if ($ip !== $user->getLastLoginIp()) {
                $user->setLastLoginIp($ip);
            }
            $user->setLastLoginAt(new \DateTimeImmutable());
            $this->em->flush();
        }
    }
}
