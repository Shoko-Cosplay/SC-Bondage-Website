<?php

namespace App\Database\Password;
use App\Database\Auth\Exception\UserNotFoundException;
use App\Database\Auth\UserRepository;
use App\Database\Password\Data\PasswordResetRequestData;
use App\Database\Password\Entity\PasswordResetToken;
use App\Database\Password\Event\PasswordRecoveredEvent;
use App\Database\Password\Event\PasswordResetTokenCreatedEvent;
use App\Database\Password\Exception\OngoingPasswordResetException;
use App\Database\Password\Repository\PasswordResetTokenRepository;
use App\Infrastructure\Security\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordService
{
    final public const EXPIRE_IN = 30;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordResetTokenRepository $tokenRepository,
        private readonly TokenGeneratorService $generator,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    /**
     * Lance une demande de réinitialisation de mot de passe.
     *
     * @throws OngoingPasswordResetException
     * @throws UserNotFoundException
     */
    public function resetPassword(PasswordResetRequestData $data): void
    {
        $user = $this->userRepository->findOneBy(['email' => $data->getEmail(), 'bannedAt' => null]);
        if (null === $user) {
            throw new UserNotFoundException();
        }
        $token = $this->tokenRepository->findOneBy(['user' => $user]);
        if (null !== $token && !$this->isExpired($token)) {
            throw new OngoingPasswordResetException();
        }
        if (null === $token) {
            $token = new PasswordResetToken();
            $this->em->persist($token);
        }
        $token->setUser($user)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setToken($this->generator->generate());
        $this->em->flush();
        $this->dispatcher->dispatch(new PasswordResetTokenCreatedEvent($token));
    }

    public function isExpired(PasswordResetToken $token): bool
    {
        $expirationDate = new \DateTimeImmutable('-'.self::EXPIRE_IN.' minutes');

        return $token->getCreatedAt() < $expirationDate;
    }

    public function updatePassword(string $password, PasswordResetToken $token): void
    {
        $user = $token->getUser();
        $user->setConfirmationToken(null);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $this->em->remove($token);
        $this->em->flush();
        $this->dispatcher->dispatch(new PasswordRecoveredEvent($user));
    }
}
