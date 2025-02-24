<?php

namespace App\Http\Controller;

use App\Infrastructure\Rss\RssFeedGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected array $params;

    public function __construct(RssFeedGenerator $rssFeedGenerator)
    {
        $package = new Package(new EmptyVersionStrategy());
        $this->params = [
            'rss' => $rssFeedGenerator->feedsArray(),
            'og_title' => '',
            'og_image' => $package->getUrl('images/logo.png'),
            'og_description' => '',
        ];
    }

    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $parameters = array_merge($parameters, $this->params);
        $parameters['title'] = $this->params['og_title'];
        $parameters['description'] = $this->params['og_description'];

        $parameters['env'] = [];
        foreach ($_ENV as $key => $value) {
            $parameters['env'][$key] = $value;
        }

        return parent::render($view, $parameters, $response);
    }
}
