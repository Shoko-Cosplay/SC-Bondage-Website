<?php

namespace App\Infrastructure\Orm;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
#[AsDoctrineListener(event: ToolEvents::postGenerateSchema)]
class DoctrineSchemaListener
{
    public function postGenerateSchema(GenerateSchemaEventArgs $event): void
    {

    }
}
