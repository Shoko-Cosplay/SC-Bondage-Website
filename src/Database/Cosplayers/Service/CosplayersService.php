<?php

namespace App\Database\Cosplayers\Service;

use App\Database\Auth\UserRepository;
use Knp\Component\Pager\PaginatorInterface;

class CosplayersService
{
    public function __construct(private readonly PaginatorInterface $paginator,private readonly UserRepository $userRepository)
    {
    }

    public function listCosplayers()
    {
        return $this->userRepository->cosplayers();
    }

    public function listCosplayersNew()
    {
        return $this->userRepository->cosplayersNew();
    }


}
