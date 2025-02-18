<?php

declare(strict_types=1);

namespace App\Http\Twig;

use App\Database\Auth\User;
use App\Database\Cosplayers\Repository\CosplayerRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class CosplayerExtension extends AbstractExtension
{

    public function __construct(private readonly CosplayerRepository $cosplayerRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('loadCosplayer',[$this,'loadCosplayer']),
        ];
    }
    public function loadCosplayer(User $user)
    {
        return $this->cosplayerRepository->findOneBy(['user'=>$user]);

    }
}
