<?php

declare(strict_types=1);

namespace App\Http\Twig;

use App\Infrastructure\Cache\GulpBusterVersionStrategy;
use Liip\ImagineBundle\Service\FilterService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class TwigOgExtension extends AbstractExtension
{
    public function __construct(private FilterService $filterService,private GulpBusterVersionStrategy $gulpBusterVersionStrategy)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ogImage', [$this,'ogImage']),
        ];
    }

    function isFacebookUserAgent(): bool
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'FBAN') !== false;
    }

    public function ogImage(string $path,string $filter): string
    {
        if($this->isFacebookUserAgent())
            return $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path,$filter).$this->gulpBusterVersionStrategy->applyVersion("");
        else
            return $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path,$filter).".webp".$this->gulpBusterVersionStrategy->applyVersion("");
    }
}
