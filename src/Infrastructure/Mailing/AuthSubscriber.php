<?php

namespace App\Infrastructure\Mailing;

use App\Database\Auth\Event\UserCreatedEvent;
use App\Database\Password\Event\PasswordResetTokenCreatedEvent;
use App\Database\Profile\DeleteAccountService;
use App\Database\Profile\Event\UserDeleteRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
class AuthSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Mailer $mailer)
    {
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordRequest',
            UserCreatedEvent::class => 'onRegister',
            UserDeleteRequestEvent::class => 'onDelete',
        ];
    }

    public function onPasswordRequest(PasswordResetTokenCreatedEvent $event): void
    {
        $email = $this->mailer->createEmail('mails/auth/password_reset.twig', [
            'token' => $event->getToken()->getToken(),
            'subject' => 'Shoko Cosplay | Réinitialisation de votre mot de passe',
            'id' => $event->getUser()->getId(),
            'username' => $event->getUser()->getUsername(),
        ])
            ->to($event->getUser()->getEmail())
            ->subject('Shoko Cosplay | Réinitialisation de votre mot de passe');
        $this->mailer->send($email);
    }

    public function onRegister(UserCreatedEvent $event): void
    {
        if ($event->isUsingOauth()) {
            return;
        }
        $email = $this->mailer->createEmail('mails/auth/register.twig', [
            'user' => $event->getUser(),
            'subject' => 'Shoko Cosplay | Confirmation du compte'
        ])
            ->to($event->getUser()->getEmail())
            ->subject('Shoko Cosplay | Confirmation du compte');
        $this->mailer->send($email);
    }

    public function onDelete(UserDeleteRequestEvent $event): void
    {
        $email = $this->mailer->createEmail('mails/auth/delete.twig', [
            'user' => $event->getUser(),
            'days' => DeleteAccountService::DAYS,
        ])
            ->to($event->getUser()->getEmail())
            ->subject('Shoko Cosplay | Suppression de votre compte');
        $this->mailer->send($email);
    }
}
