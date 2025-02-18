<?php

namespace App\Database\Cosplayers\Entity;

use App\Infrastructure\Vault\VaultClient;
use App\Repository\Database\Cosplayers\Entity\CosplayersSocialRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;


#[ORM\Table(name: '`cosplayers_social`')]
#[AsDoctrineListener(Events::prePersist)]
#[AsDoctrineListener(Events::preUpdate)]
#[AsDoctrineListener(Events::postLoad)] #[ORM\Entity(repositoryClass: CosplayersSocialRepository::class)]
class CosplayersSocial
{
    private VaultClient $vaultClient;

    public function __construct()
    {
        $this->vaultClient = new VaultClient($_ENV['APP_ENV']);
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cosplayersSocials')]
    private ?Cosplayers $cosplayer = null;

    #[ORM\Column(length: 255)]
    private ?string $network = null;

    #[ORM\Column(type: 'text')]
    private ?string $link = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCosplayer(): ?Cosplayers
    {
        return $this->cosplayer;
    }

    public function setCosplayer(?Cosplayers $cosplayer): static
    {
        $this->cosplayer = $cosplayer;

        return $this;
    }

    public function getNetwork(): ?string
    {
        return $this->network;
    }

    public function setNetwork(string $network): static
    {
        $this->network = $network;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof CosplayersSocial) {
            return;
        }

        $entity->setLink($this->vaultClient->encrypt(VaultClient::KEYS_COSPLAYER_SOCIAL,$entity->getLink()));
    }
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof CosplayersSocial) {
            return;
        }

        $entity->setLink($this->vaultClient->encrypt(VaultClient::KEYS_COSPLAYER_SOCIAL,$entity->getLink()));
    }
    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof CosplayersSocial) {
            return;
        }

        $entity->setLink($this->vaultClient->decrypt(VaultClient::KEYS_COSPLAYER_SOCIAL,$entity->getLink()));
    }
}
