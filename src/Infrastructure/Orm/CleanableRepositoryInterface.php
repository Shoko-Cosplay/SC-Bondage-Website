<?php

namespace App\Infrastructure\Orm;

interface CleanableRepositoryInterface
{
    public function clean(): int;
}
