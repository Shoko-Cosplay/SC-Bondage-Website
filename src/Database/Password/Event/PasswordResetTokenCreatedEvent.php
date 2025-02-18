<?php

namespace App\Database\Password\Event;

use App\Database\Auth\User;
use App\Database\Password\Entity\PasswordResetToken;

final readonly class PasswordResetTokenCreatedEvent
{
    public function __construct(private PasswordResetToken $token)
    {
    }

    public function getUser(): User
    {
        return $this->token->getUser();
    }

    public function getToken(): PasswordResetToken
    {
        return $this->token;
    }
}
