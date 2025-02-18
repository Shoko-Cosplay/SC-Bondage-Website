<?php

namespace App\Http\Controller;

use App\Infrastructure\Rss\RssFeedGenerator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RssController extends AbstractController
{
    #[Route('/rss', name: 'app_rss')]
    public function rss(RssFeedGenerator $rssFeedGenerator) : Response
    {
        $items = [];
        foreach ($rssFeedGenerator->feeds() as $feed) {
            $items[$feed] = $this->generateUrl('app_rss_show',['type'=>$feed],UrlGeneratorInterface::ABSOLUTE_URL);
        }
        return  $this->json([
            'feeds' => $items,
        ]);
    }
    #[Route('/rss/{type}', name: 'app_rss_show')]
    public function rssShow(string $type, RssFeedGenerator $feedGenerator) : Response
    {
        if (!$feedGenerator->has($type)) {
            throw $this->createNotFoundException('Flux non trouvé');
        }
        return $feedGenerator->generatedFeed([],$type);
    }
}
