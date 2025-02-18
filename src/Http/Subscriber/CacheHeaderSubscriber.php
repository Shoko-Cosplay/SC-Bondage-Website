<?php

namespace App\Http\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CacheHeaderSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            ResponseEvent::class => 'onCacheHeader',
        ];
    }
    public function onCacheHeader(ResponseEvent $event)
    {
        if($_ENV['APP_ENV'] == "prod") {

            $response = $event->getResponse();
            $response->setCache([
                'public' => true,
                'max_age' => 86400,
                's_maxage' => 86400,
            ]);
            $event->setResponse($response);
            $event->stopPropagation();

        }

    }
}
