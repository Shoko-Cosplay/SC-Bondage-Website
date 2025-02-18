<?php

namespace App\Database\Cosplayers\Repository;

use App\Database\Cosplayers\Entity\Cosplayers;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Cosplayers>
 */
class CosplayerRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cosplayers::class);
    }


}
