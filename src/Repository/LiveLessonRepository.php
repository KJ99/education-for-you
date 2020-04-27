<?php

namespace App\Repository;

use App\Entity\LiveLesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LiveLesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method LiveLesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method LiveLesson[]    findAll()
 * @method LiveLesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiveLessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiveLesson::class);
    }

    // /**
    //  * @return LiveLesson[] Returns an array of LiveLesson objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LiveLesson
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
