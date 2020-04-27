<?php

namespace App\Repository;

use App\Entity\LessonView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LessonView|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonView|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonView[]    findAll()
 * @method LessonView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonView::class);
    }

    // /**
    //  * @return LessonView[] Returns an array of LessonView objects
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
    public function findOneBySomeField($value): ?LessonView
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
