<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /**
     * Fetches all sessions sorted by Level Name then by Start Date
     */
    public function findAllSorted(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.level', 'l')
            ->leftJoin('s.sessionDates', 'sd')
            ->addSelect('l', 'sd') // Eager load
            ->orderBy('l.levelName', 'ASC')
            ->addOrderBy('sd.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
