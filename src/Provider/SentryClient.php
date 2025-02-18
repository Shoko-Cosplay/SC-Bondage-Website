<?php

namespace App\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Sentry\EventId;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SentryClient
{

    public function __construct(private readonly EntityManagerInterface $em)
    {
        \Sentry\init([
            'dsn' => $_ENV['SENTRY_DNS'],
            'traces_sample_rate' => 1.0,
            'profiles_sample_rate' => 1.0,
            'environment' => $_ENV["APP_ENV"],
            'release' => 'slave@'.$_ENV['VERSION'],
            'before_send' => function (\Sentry\Event $event, ?\Sentry\EventHint $hint): \Sentry\Event {
                $this->saveError($event->getId());

                return $event;
            },
        ]);
    }

    private function saveError(EventId $id) : void
    {
        $this->em->flush();


    }
    public function sendException(\Exception $exception) : void
    {
        \Sentry\captureException($exception);

    }
}
