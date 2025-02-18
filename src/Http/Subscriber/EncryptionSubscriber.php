<?php

namespace App\Http\Subscriber;

use App\Infrastructure\Vault\SensitiveData;
use App\Infrastructure\Vault\VaultClient;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EncryptionSubscriber implements EventSubscriberInterface
{
    private VaultClient $vaultClient;


    public static function getSubscribedEvents(): array
    {
return  [];
        return [
            Events::prePersist=> ['prePersist',10],
            Events::preUpdate =>['preUpdate',10],
            Events::postLoad =>['postLoad',10],
        ];
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        dd($args);
        $this->encryptFields($args->getObject());

    }
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->encryptFields($args->getObject());
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $this->decryptFields($args->getObject());
    }

    public function encryptFields(object $entity): void
    {
        $reflection = new ReflectionClass($entity);
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(SensitiveData::class);
            if ($attributes) {
                $property->setAccessible(true);
                $value = $property->getValue($entity);
                if ($value !== null) {
                    $encryptAttribute = $attributes[0]->newInstance();
                    $encryptedValue = $this->vaultClient->encrypt($encryptAttribute->keyName,$value);
                    $property->setValue($entity, $encryptedValue);
                }
            }
        }
    }

    private function decryptFields(object $entity): void
    {
        $reflection = new ReflectionClass($entity);
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(SensitiveData::class);
            if ($attributes) {
                $property->setAccessible(true);
                $value = $property->getValue($entity);
                if ($value !== null) {
                    $encryptAttribute = $attributes[0]->newInstance();
                    $decryptedValue = $this->vaultClient->decrypt($encryptAttribute->keyName,$value);
                    $property->setValue($entity, $decryptedValue);
                }
            }
        }
    }


}
