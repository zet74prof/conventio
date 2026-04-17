<?php

namespace App\Repository;

use App\Entity\Level;
use App\Entity\Professor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Level>
 */
class LevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Level::class);
    }

    //    /**
    //     * @return Level[] Returns an array of Level objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    /**
     * @return Level[] Returns an array of Level objects with students and contracts for a given professor
     */
    public function findLevelsWithStudentsAndContracts(Professor $professor): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.professors', 'p')
            ->leftJoin('l.students', 's')
            ->leftJoin('s.contracts', 'c')
            ->addSelect('s', 'c')
            ->where('p = :professor')
            ->setParameter('professor', $professor)
            ->orderBy('l.levelName', 'ASC')
            ->addOrderBy('s.lastname', 'ASC')
            ->addOrderBy('s.firstname', 'ASC')
            ->addOrderBy('c.status', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
