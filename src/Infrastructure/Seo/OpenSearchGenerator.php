<?php

namespace App\Infrastructure\Seo;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OpenSearchGenerator
{
    public function __construct(private UrlGeneratorInterface $urlGeneratorInterface)
    {
    }

    public function generateOpenSearchDescription()
    {
        $shortName = "Shoko Cosplay";
        $description = 'Recherchez sur mon site';
        $searchUrl = $this->urlGeneratorInterface->generate('app_opensearch', [], UrlGeneratorInterface::ABSOLUTE_URL);
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
    <ShortName>$shortName</ShortName>
    <Description>$description</Description>
    <Url type="text/html" template="$searchUrl?q={searchTerms}"/>
</OpenSearchDescription>
XML;
    }
}

