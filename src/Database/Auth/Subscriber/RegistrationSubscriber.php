<?php

namespace App\Database\Auth\Subscriber;

use App\Database\Auth\Event\UserBeforeCreatedEvent;
use App\Database\Auth\Service\RegistrationDurationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RegistrationSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly RegistrationDurationService $registrationDurationService)
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequest',
            UserBeforeCreatedEvent::class => 'onRegister',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        // La requête ne concerne pas l'inscription
        if (
            $event->getRequest()->attributes->get('_route') !== 'register'
            || !$event->getRequest()->isMethod('GET')
        ) {
            return;
        }

        $this->registrationDurationService->startTimer($event->getRequest());
    }

    public function onRegister(UserBeforeCreatedEvent $event): void
    {
        $event->user->setRegistrationDuration($this->registrationDurationService->getDuration($event->request));
    }
}
