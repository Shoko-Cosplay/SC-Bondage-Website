<?php

namespace App\Infrastructure\Legal;

use Symfony\Component\HttpKernel\KernelInterface;

class CreditServices
{
    const BASEFILE = [
        ['type'=>'image','author'=>'Shoko Cosplay','link'=>'https://www.shoko-cosplay.fr','path'=>'/images/logo.png'],
    ];

    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    /**
     * @return CreditItem[]
     */
    public function listCredit()
    {
        $lists =[];
        foreach (self::BASEFILE as $file) {
            $itemFile = new CreditItem();
            $itemFile->setType($file['type']);
            $itemFile->setAuthor($file['author']);
            $itemFile->setLink($file['link']);
            $itemFile->setPath($file['path']);

            $lists[$itemFile->getType()][] = $itemFile;
        }

        return $lists;

    }

    public function listLibs()
    {

        $libs = [
            'php' => [],
            'node' => [],
        ];

        //composer.json
        $composerFile = $this->kernel->getProjectDir()."/composer.json";
        $composerToStdClass = json_decode(file_get_contents($composerFile));


        foreach ($composerToStdClass->require as $key => $version) {
            if(!str_contains($key,"ext-") && $key != "php") {
                $libs['php'][$key] = "https://packagist.org/packages/" . $key;
            }
        }
        //p ackages.json
        $packageFile = $this->kernel->getProjectDir()."/package.json";
        $packageFileToStdClass = json_decode(file_get_contents($packageFile));
        foreach ($packageFileToStdClass->dependencies as $key => $package) {
            $libs['node'][$key] = "https://www.npmjs.com/package/".$key;
        }

        return $libs;
    }

}
