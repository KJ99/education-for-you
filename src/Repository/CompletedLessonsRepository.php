<?php

namespace App\Repository;

use App\Entity\CompletedLessons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CompletedLessons|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompletedLessons|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompletedLessons[]    findAll()
 * @method CompletedLessons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompletedLessonsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompletedLessons::class);
    }

    // /**
    //  * @return CompletedLessons[] Returns an array of CompletedLessons objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompletedLessons
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
