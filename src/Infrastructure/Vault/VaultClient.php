<?php

namespace App\Infrastructure\Vault;

use GuzzleHttp\Client;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\SecretEngines\Engines\Transit\EncryptionType;
use VaultPHP\SecretEngines\Engines\Transit\Request\CreateKeyRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

class VaultClient
{
    private \VaultPHP\VaultClient $vaultClient;
    private mixed $config;

    public const KEYS_COSPLAYER = "cosplayers";
    public const KEYS_COSPLAYER_SOCIAL = "cosplayers_social";

    public function __construct(string $env)
    {
        $dir = __DIR__."/../../../var/vault-{$env}.json";
        $this->config = json_decode(file_get_contents($dir));

        $httpClient = new Client();
        $authToken = new Token($this->config->root_token);

        $this->vaultClient = new \VaultPHP\VaultClient($httpClient,$authToken,$_ENV['VAULT_ADDR']);

    }

    public function unlock()
    {
        $http = new Client();
        try {
            $response = $http->request('GET', $_ENV['VAULT_ADDR'] . "/v1/sys/seal-status");
            $content = json_decode($response->getBody()->getContents());
            if($content->sealed) {
                $keys = $this->config->keys;
                foreach ($keys as $key) {
                    $http->request('POST',$_ENV['VAULT_ADDR']."/v1/sys/unseal",[
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'key' => $key
                        ]
                    ]);
                }
            }
        }catch (\Exception $e) {
        }
    }

    public function secretEngine(string $key)
    {
        $transitApi = new Transit($this->vaultClient);

        $listKey = [];
        foreach ($transitApi->listKeys()->getKeys() as $key) {
            $listKey[] = $key;
        }

        if(!in_array($key,$listKey)){
            $requestCreated = new CreateKeyRequest($key);
            $requestCreated->setType(EncryptionType::AES_256_GCM_96);
            $transitApi->createKey($requestCreated);
        }
    }

    public function encrypt(string$keyName, string$value) :string
    {
        $transitApi = new Transit($this->vaultClient);
        $encryptExample = new EncryptDataRequest($keyName, $value);
        try {
            $encryptResponse = $transitApi->encryptData($encryptExample);
        } catch (\Exception $exception){
            return  "Impossible de protéger les données";
        }
        return $encryptResponse->getCiphertext();
    }

    public function decrypt(string $keyName, string$cryptedData) : string
    {
        $transitApi = new Transit($this->vaultClient);
        $decryptExample = new DecryptDataRequest($keyName,$cryptedData);
        try {
            $decryptResponse = $transitApi->decryptData($decryptExample);
        } catch (\Exception $exception) {
            return  "Impossible de récupérer les données";
        }
        return $decryptResponse->getPlaintext();
    }

}
