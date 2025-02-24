<?php

namespace App\Http\Controller\Dashboard;

use App\Http\Controller\BaseController;
use App\Infrastructure\Rss\RssFeedGenerator;
use Symfony\Component\HttpFoundation\Response;

class DashboardBaseController extends BaseController
{
    public function __construct(RssFeedGenerator $rssFeedGenerator)
    {
        parent::__construct($rssFeedGenerator);
    }

    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $parameters['typeDashboard'] = true;

        return parent::render($view, $parameters, $response);
    }
}
