<?php

namespace App\Http\Subscriber;

use Doctrine\ORM\Event\PrePersistEventArgs;

class EntityListener
{

    public function __construct()
    {
    }
    public function prePersist(PrePersistEventArgs $args): void
    {
        dd($args);
    }
}
