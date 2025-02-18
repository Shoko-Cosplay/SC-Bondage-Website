<?php

namespace App\Infrastructure\Vault;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SensitiveData
{
    public function __construct(
        public string $keyName
    ){}
}
