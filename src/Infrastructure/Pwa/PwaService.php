<?php

namespace App\Infrastructure\Pwa;

use App\Infrastructure\Cache\GulpBusterVersionStrategy;
use Symfony\Component\Asset\Package;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PwaService
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function generateManifest() : array
    {
        $package = new Package(new GulpBusterVersionStrategy());

        return [
            'name' => 'Slave Control',
            'short_name' => 'SC',
            'start_url' =>  $this->urlGenerator->generate('app_root'),
            'scope' =>  $this->urlGenerator->generate('app_root'),
            'background_color' => '#fff',
            'theme_color' => '#000',
            "display" => "browser",
            'icons' => [
                [
                    'src' => '/'.$package->getUrl('favicon/favicon-16x16.png'),
                    'sizes' => '16x16',
                    'type' => 'image/png',
                ],
                [
                    'src' => '/'.$package->getUrl('favicon/favicon-32x32.png'),
                    'sizes' => '32x32',
                    'type' => 'image/png',
                ],
                [
                    'src' => '/'.$package->getUrl('favicon/favicon-96x96.png'),
                    'sizes' => '32x32',
                    'type' => 'image/png',
                ],
                [
                    'src' => '/'.$package->getUrl('favicon/favicon-256x256.png'),
                    'sizes' => '32x32',
                    'type' => 'image/png',
                ],

            ],
        ];
    }
}
