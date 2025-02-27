<?php

namespace App\Database\Auth\Service;


use App\Database\Auth\Entity\LoginAttempt;
use App\Database\Auth\Repository\LoginAttemptRepository;
use App\Database\Auth\User;
use Doctrine\ORM\EntityManagerInterface;

class LoginAttemptService
{
    final public const ATTEMPTS = 3;

    public function __construct(private readonly LoginAttemptRepository $repository, private readonly EntityManagerInterface $em)
    {
    }

    public function addAttempt(User $user): void
    {
        // TODO : Envoyer un email au bout du Xème essai
        $attempt = (new LoginAttempt())->setUser($user);
        $this->em->persist($attempt);
        $this->em->flush();
    }

    public function limitReachedFor(User $user): bool
    {
        return $this->repository->countRecentFor($user, 30) >= self::ATTEMPTS;
    }
}
