<?php

namespace App\Tests\Provider;

use App\Provider\VaultClient;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VaultClientTest extends KernelTestCase
{

    public function testInstance()
    {
        static::bootKernel();

        $vaultClient = $this->createMock(VaultClient::class);
        $this->assertInstanceOf(VaultClient::class, $vaultClient);
    }

    public function testRooToken()
    {
        static::bootKernel();

        $httpClientMock = $this->getMockForAbstractClass(HttpClientInterface::class);

        $vaultClient = new VaultClient($httpClientMock);
        $this->assertEquals($_ENV['VAULT_KEY'], $vaultClient->getRootToken() ?? null);
        $this->assertEquals($_ENV['VAULT_DNS'], $vaultClient->getVaultAddress() ?? null);
    }
    public function testIsSeal()
    {
        static::bootKernel();
        $responseMock = $this->createMock(\Symfony\Contracts\HttpClient\ResponseInterface::class);
        $responseMock->method('getContent')->willReturn('{"sealed": false}'); // Simule une réponse JSON

        $httpClientMock = $this->createMock(\Symfony\Contracts\HttpClient\HttpClientInterface::class);
        $httpClientMock->method('request')->willReturn($responseMock);

        $vaultClient = new VaultClient($httpClientMock);
        $result = $vaultClient->isSeal();
        $this->assertEquals(false, $result);

    }

    public function testIsSealException()
    {
        $httpClientMock = $this->createMock(\Symfony\Contracts\HttpClient\HttpClientInterface::class);
        $httpClientMock->method('request')->willThrowException(new \Exception("Erreur réseau"));
        $vaultClient = new VaultClient($httpClientMock);
        $this->assertTrue($vaultClient->isSeal());
    }

}
