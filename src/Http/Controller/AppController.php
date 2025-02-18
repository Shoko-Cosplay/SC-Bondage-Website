<?php

namespace App\Http\Controller;

use App\Infrastructure\Uploader\AwsS3Storage;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AppController extends BaseController
{
    #[Route('/', name: 'app_root',options: ['sitemap' => ['priority' => 1.0, 'changefreq' => UrlConcrete::CHANGEFREQ_HOURLY]])]
    public function app(AuthenticationUtils $authenticationUtils)
    {

        $this->params['og_title'] = 'Accueil';
        return $this->render('app/home.twig',[
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }
}
