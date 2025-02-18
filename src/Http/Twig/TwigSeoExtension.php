<?php

namespace App\Http\Twig;

use App\Databse\Auth\User;
use App\Databse\Blog\Post;
use App\Infrastructure\Seo\SeoAltImage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelperInterface;

class TwigSeoExtension extends AbstractExtension
{
    public function __construct(
        private readonly SeoAltImage $seoAltImage
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('seoAltImage', $this->seoAltImage(...)),
        ];
    }
    public function seoAltImage(string $message,string $locale,$isSystem=false)
    {
      return $this->seoAltImage->trans($message,$locale,$isSystem);
    }
}
