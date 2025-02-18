<?php

namespace App\Infrastructure\Rss;

use Eko\FeedBundle\Feed\FeedManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class RssFeedGenerator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private FeedManager $feedManager,
        private KernelInterface $kernelInterface,
        private TranslatorInterface $translatorInterface,
        private readonly RequestStack $request){


    }

    public function generatedFeed(array $items,string $feedName = "articles") : Response
    {
        $feed = $this->feedManager->get($feedName);
        $feed->set('title','Shoko Cosplay - '.$this->translatorInterface->trans($feedName));
        $feed->set('description','Shoko Cosplay - '.$this->translatorInterface->trans('desc_'.$feedName));
        $feed->set('link', $this->request->getCurrentRequest()->getSchemeAndHttpHost());
        $feed->addFromArray($items);

        return new Response($feed->render('rss'),200,[
            'Content-Type' => 'application/rss+xml; charset=utf-8',
        ]);
    }

    public function feedsArray() : array
    {
        $path = $this->kernelInterface->getProjectDir()."/config/packages/eko_feed.yaml";
        $parsed = Yaml::parseFile($path)['eko_feed']['feeds'];
        $keys =  array_keys($parsed);
        $items = [];

        foreach ($keys as $key) {

            $items[] = [
                'name' => 'Shoko Cosplay - '.$this->translatorInterface->trans($key),
                'url' => $this->urlGenerator->generate('app_rss_show',['type'=>$key]),
            ];
        }
        return  $items;
    }
    public function feeds()
    {
        $path = $this->kernelInterface->getProjectDir()."/config/packages/eko_feed.yaml";
        $parsed = Yaml::parseFile($path)['eko_feed']['feeds'];
        return array_keys($parsed);
    }

    public function has(string $name)
    {
        return $this->feedManager->has($name);
    }
}
