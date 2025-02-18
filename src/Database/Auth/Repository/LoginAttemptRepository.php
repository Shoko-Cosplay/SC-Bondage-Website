<?php

namespace App\Database\Auth\Repository;

use App\Database\Auth\Entity\LoginAttempt;
use App\Database\Auth\User;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<LoginAttempt>
 */
class LoginAttemptRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginAttempt::class);
    }

    /**
     * Compte le nombre de tentative de connexion pour un utilisateur.
     */
    public function countRecentFor(User $user, int $minutes): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id) as count')
            ->where('l.user = :user')
            ->andWhere('l.createdAt > :date')
            ->setParameter('date', new \DateTimeImmutable("-{$minutes} minutes"))
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteAttemptsFor(User $user): void
    {
        $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }
}
