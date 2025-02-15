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
    private string $unsealToken = "";

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
        $this->rootToken = $_ENV['VAULT_KEY'];
        $this->vaultAddress = $_ENV['VAULT_DNS'];
        $this->unsealToken = $_ENV['VAULT_UNSEAL'];
    }

    public function seal(): true
    {
        try {
            $this->httpClient->request("POST", $this->vaultAddress . "/v1/sys/seal", [
                'headers' => [
                    'X-Vault-Token' => $this->rootToken,
                ]
            ]);
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {

        }
        return true;
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

    public function unseal(): bool
    {
        try {
             $this->httpClient->request('POST',$this->vaultAddress."/v1/sys/unseal",[
                 'headers' => [
                     'X-Vault-Token' => $this->rootToken,
                 ],
                 'body' => [
                     'key' => $this->unsealToken
                 ]
             ]);
             return true;
        }catch (\Exception $exception){
            return false;
        }
    }
}
