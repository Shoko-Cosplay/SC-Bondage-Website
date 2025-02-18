<?php

namespace App\Http\Api\Controller;

use App\Http\Controller\BaseController;
use App\Infrastructure\Spam\GeoIpService;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DisplayAddressController extends BaseController
{

    #[Route('/displayAddress', name: 'apo_displayAddress')]
    public function displayAddress()
    {
        return $this->json([
            'email' => 'contact[at]shoko-cosplay.fr'
        ],Response::HTTP_OK);
    }
}

