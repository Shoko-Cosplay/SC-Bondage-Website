<?php

namespace App\Infrastructure\Cache;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class GulpBusterVersionStrategy implements VersionStrategyInterface
{
    private \DateTimeImmutable $version;

    public function __construct()
    {
        $this->version = new \DateTimeImmutable();
    }

    public function getVersion(string $path): string
    {
        return $this->version->format('d-m-Y');
    }

    public function applyVersion(string $path): string
    {
        return sprintf('%s?v=%s', $path, $this->getVersion($path));
    }


}
