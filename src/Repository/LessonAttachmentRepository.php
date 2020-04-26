<?php

namespace App\Repository;

use App\Entity\LessonAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LessonAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonAttachment[]    findAll()
 * @method LessonAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonAttachment::class);
    }

    // /**
    //  * @return LessonAttachment[] Returns an array of LessonAttachment objects
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
    public function findOneBySomeField($value): ?LessonAttachment
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
