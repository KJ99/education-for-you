<?php

namespace App\Repository;

use App\Entity\LessonVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LessonVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonVideo[]    findAll()
 * @method LessonVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonVideo::class);
    }

    // /**
    //  * @return LessonVideo[] Returns an array of LessonVideo objects
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
    public function findOneBySomeField($value): ?LessonVideo
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
