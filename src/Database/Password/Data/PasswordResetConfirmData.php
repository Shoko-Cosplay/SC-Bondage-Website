<?php

namespace App\Database\Password\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class PasswordResetConfirmData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password = '';

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
