<?php

declare(strict_types=1);

namespace App\Http\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class TwigLegalExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('tierService', $this->tierService(...)),
        ];
    }


    public function tierService(): array
    {
        $services = [
            'google_fonts' => ['link'=>'https://fonts.google.com/knowledge/using_type/using_web_fonts','title'=>'Service de police de Caractères Google','label'=>'Google Fonts'],
            'fontawesome' => ['link'=>'https://fontawesome.com','title'=>'Service de police de Caractères Google','label'=>'Font Awesome'],
            'hrecaptcha' => ['link'=>'https://www.hcaptcha.com','title'=>'Service de protection contre les robots','label'=>'HCaptcha'],
            'sentry' => ['link'=>'https://sentry.io','title'=>'Survélliance des erreurs','label'=>'Sentry'],
            'plausible' => ['link'=>'https://analytics.shoko-cosplay.fr','title'=>'Outils d\'analytics de traffic','label'=>'Plausible (Auto hébergée)'],
            'stancer' => ['link'=>'https://stancer.com/fr/','title'=>'Plateforme de paiment','label'=>'Stancer'],
            'scalepay' => ['link'=>'https://www.scalapay.com/fr','title'=>'Plateforme de paiements échelonné','label'=>'Scalepay'],
            'colissimo' => ['link'=>'https://www.laposte.fr/colissimo','title'=>'Service de livraison','label'=>'LaPoste'],
            'mondialRelay' => ['link'=>'https://www.mondialrelay.fr','title'=>'Service de livraison','label'=>'Mondial Relay'],
        ];


        return $services;
    }
}
