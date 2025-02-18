<?php

namespace App\Database\Cosplayers\Entity;

use App\Database\Auth\User;
use App\Database\Cosplayers\Repository\CosplayerRepository;
use App\Infrastructure\Vault\SensitiveData;
use App\Infrastructure\Vault\VaultClient;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[ORM\Table(name: '`cosplayers`')]
#[AsDoctrineListener(Events::prePersist)]
#[AsDoctrineListener(Events::preUpdate)]
#[AsDoctrineListener(Events::postLoad)]
#[ORM\Entity(repositoryClass: CosplayerRepository::class)]
class Cosplayers
{

    private VaultClient $vaultClient;
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[SensitiveData('cosplayer')]
    private string $pseudo;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, CosplayersSocial>
     */
    #[ORM\OneToMany(targetEntity: CosplayersSocial::class, mappedBy: 'cosplayer')]
    private Collection $cosplayersSocials;

    public function __construct()
    {
        $this->vaultClient = new VaultClient($_ENV['APP_ENV']);
        $this->cosplayersSocials = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }


    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Cosplayers) {
            return;
        }

        $entity->setPseudo($this->vaultClient->encrypt(VaultClient::KEYS_COSPLAYER,$entity->getPseudo()));
        if($entity->getDescription() != null){
            $this->setDescription($this->vaultClient->encrypt(VaultClient::KEYS_COSPLAYER,$entity->getDescription()));
        }

    }
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Cosplayers) {
            return;
        }

        $entity->setPseudo($this->vaultClient->encrypt(VaultClient::KEYS_COSPLAYER,$entity->getPseudo()));
        if($entity->getDescription() != null){
            $this->setDescription($this->vaultClient->encrypt(VaultClient::KEYS_COSPLAYER,$entity->getDescription()));
        }

    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Cosplayers) {
            return;
        }

        $entity->setPseudo($this->vaultClient->decrypt(VaultClient::KEYS_COSPLAYER,$entity->getPseudo()));
        if($entity->getDescription() != null){
            $this->setDescription($this->vaultClient->decrypt(VaultClient::KEYS_COSPLAYER,$entity->getDescription()));
        }
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, CosplayersSocial>
     */
    public function getCosplayersSocials(): Collection
    {
        return $this->cosplayersSocials;
    }

    public function addCosplayersSocial(CosplayersSocial $cosplayersSocial): static
    {
        if (!$this->cosplayersSocials->contains($cosplayersSocial)) {
            $this->cosplayersSocials->add($cosplayersSocial);
            $cosplayersSocial->setCosplayer($this);
        }

        return $this;
    }

    public function removeCosplayersSocial(CosplayersSocial $cosplayersSocial): static
    {
        if ($this->cosplayersSocials->removeElement($cosplayersSocial)) {
            // set the owning side to null (unless already changed)
            if ($cosplayersSocial->getCosplayer() === $this) {
                $cosplayersSocial->setCosplayer(null);
            }
        }

        return $this;
    }}
