<?php

namespace App\Provider;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VaultClient
{
    private string $rootToken = "";
    private string $vaultAddress = "";

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
        $this->rootToken = $_ENV['VAULT_KEY'];
        $this->vaultAddress = $_ENV['VAULT_DNS'];
    }


    public function isSeal() : bool
    {
        try {
            $response = $this->httpClient->request("GET", $this->vaultAddress . "/v1/sys/seal-status", [
                'headers' => [
                    'X-Vault-Token' => $this->rootToken,
                ]
            ]);
            $content = json_decode($response->getContent());
            return $content->sealed;
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return true;
        }
    }
}
