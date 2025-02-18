<?php

namespace App\Infrastructure\Redis;

class Client
{
    private \Redis $client;

    public function __construct()
    {
        $this->client = new \Redis([
            'host' => $_ENV['REDIS_HOST'],
            'port' => intval($_ENV['REDIS_PORT']),
        ]);
    }

    public function exist(string $key){
        try {
            return $this->client->exists($key);
        } catch (\RedisException $e) {
            return false;
        }
    }
    public function get(string $key)
    {
        return $this->client->get($key);
    }
    public function set(string $key, $value, int $expire = 0)
    {
        $this->client->set($key,$value);
        if($expire>0) {
            $this->client->expire($key, $expire);
        }
    }
}
