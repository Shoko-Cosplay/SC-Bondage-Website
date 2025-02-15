<?php

namespace App\Tests\Provider;

use App\Provider\VaultClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VaultClientTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testCreated(): void
    {
        $vaultClient = $this->createMock(VaultClient::class);
        $this->assertInstanceOf(VaultClient::class,$vaultClient);
    }

    public function testSeal() : void
    {
        $vaultClient = $this->createMock(VaultClient::class);
        $this->assertFalse($vaultClient->isSeal());

    }

}
