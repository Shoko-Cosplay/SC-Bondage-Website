<?php

namespace App\Database\Auth\Security;

use App\Database\Auth\Exception\TooManyBadCredentialsException;
use App\Database\Auth\Exception\UserBannedException;
use App\Database\Auth\Exception\UserNotFoundException;
use App\Database\Auth\Service\LoginAttemptService;
use App\Database\Auth\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Bloque l'authentification de l'utilisateur.
 */
class UserChecker implements UserCheckerInterface
{
    public function __construct(private readonly LoginAttemptService $loginAttemptService)
    {
    }

    /**
     * Vérifie que l'utilisateur a le droit de se connecter.
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User && $this->loginAttemptService->limitReachedFor($user)) {
            throw new TooManyBadCredentialsException();
        }

        return;
    }

    /**
     * Vérifie que l'utilisateur connecté a le droit de continuer.
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if ($user instanceof User && $user->isBanned()) {
            throw new UserBannedException();
        }
        if ($user instanceof User && null !== $user->getConfirmationToken()) {
            throw new UserNotFoundException();
        }

        return;
    }
}
