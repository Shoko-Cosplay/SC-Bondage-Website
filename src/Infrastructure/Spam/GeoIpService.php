<?php

namespace App\Infrastructure\Spam;

use App\Infrastructure\Redis\Client;
use MaxMind\Db\Reader;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoIpService
{
    private string $pathBdCountry;
    private string $pathASNCountry;
    private string $sessionId;

    public function __construct(KernelInterface $kernel,RequestStack $requestStack,private readonly Client $redisClient,private readonly HttpClientInterface $httpClient)
    {
        $this->pathBdCountry = $kernel->getProjectDir()."/var/GeoLite2-Country.mmdb";
        $this->pathASNCountry = $kernel->getProjectDir()."/var/GeoLite2-ASN.mmdb";

        if(!$requestStack->getSession()->isStarted())
            $requestStack->getSession()->start();
        $this->sessionId = $requestStack->getSession()->getId();

    }

    public function fallbackIp() :string
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function getRealIp() : string
    {
        if(!$this->redisClient->exist('ip-'.$this->sessionId)){
            $ip = null;
            $urls = [
                'https://api.ipify.org?format=json',
                'https://api.my-ip.io/v2/ip.json',
            ];
            foreach ($urls as $url) {
                try {
                    $response = $this->httpClient->request('GET',$url);
                    $response = json_decode($response->getContent());
                    if(isset($response->ip)){
                        $ip = $response->ip;
                        break;
                    }
                }catch (\Exception $exception){

                }
            }
            if(is_null($ip))
                $ip = $this->fallbackIp();

            $this->redisClient->set('ip-'.$this->sessionId,$ip);
        }
        return $this->redisClient->get('ip-'.$this->sessionId);
    }

    public function country(): array
    {
        $ip = $this->getRealIp();

        $country = [];
        $countryItem = $this->getReaderCountry()->get($ip);
        if(!is_null($countryItem)){
            $country = [
                'name' => $countryItem['country']['names']['fr'],
                'iso' => $countryItem['country']['iso_code'],
            ];
        }


        return $country;
    }


    public function asn(): array
    {
        $ip = $this->getRealIp();

        $asn = [];
        $asnItem = $this->getReaderASN()->get($ip);
        if(!is_null($asnItem)){

            $asn = [
                'numberId' => $asnItem['autonomous_system_number'],
                'name' => $asnItem['autonomous_system_organization']
            ];
        }


        return $asn;
    }

    private function getReaderCountry(): Reader
    {
        return new Reader($this->pathBdCountry);
    }

    private function getReaderASN(): Reader
    {
        return new Reader($this->pathASNCountry);
    }
}
