<?php

namespace App\Database\Auth\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserBannedException extends CustomUserMessageAuthenticationException
{
    public function __construct(string $message = 'Ce compte a été bloqué', array $messageData = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
