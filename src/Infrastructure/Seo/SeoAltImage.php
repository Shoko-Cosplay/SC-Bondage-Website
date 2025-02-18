<?php

namespace App\Infrastructure\Seo;

use Symfony\Contracts\Translation\TranslatorInterface;

class SeoAltImage
{
    public function __construct(private readonly TranslatorInterface $translator)
    {

    }

    public function trans(string $message, string $locale, mixed $isSystem = false)
    {
        if($isSystem) {
            return $this->translator->trans($message, [], "messages", $locale);
        } else {

        }
    }
}
