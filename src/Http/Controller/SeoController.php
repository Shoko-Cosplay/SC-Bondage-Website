<?php

namespace App\Http\Controller;

use App\Infrastructure\Rss\RssFeedGenerator;
use App\Infrastructure\Seo\OpenSearchGenerator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SeoController extends AbstractController
{
    #[Route('/robots.txt', name: 'robots_txt', defaults: ['_format' => 'txt'])]
    public function robots() : Response
    {
        // Contenu du fichier robots.txt
        $content = "User-agent: *\n";

        // Bloquer l'indexation en environnement de développement
        if ($this->getParameter('kernel.environment') === 'dev') {
            $content .= "Disallow: /\n";
        } else {
            // Autoriser l'indexation en production
            $content .= "Allow: /\n";
        }

        $bannedPage = ["rgpd","cgv","cgu","mentions-legal"];
        foreach ($bannedPage as $page) {
            $content .= "Disallow: /" . $page . "\n";
        }

        // Ajouter des règles supplémentaires si nécessaire
        $content .= "Sitemap: ".$this->generateUrl('PrestaSitemapBundle_index',['_format'=>'xml'],UrlGeneratorInterface::ABSOLUTE_URL)."\n";


        return new Response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
    #[Route('/opensearch.xml', name: 'app_opensearch',methods: ['GET'])]
    public function opensearch(OpenSearchGenerator $openSearchGenerator) : Response
    {

        return new Response($openSearchGenerator->generateOpenSearchDescription(), 200, ['Content-Type' => 'application/opensearchdescription+xml']);
    }
}
