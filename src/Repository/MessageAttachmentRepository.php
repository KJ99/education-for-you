<?php

namespace App\Repository;

use App\Entity\MessageAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MessageAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageAttachment[]    findAll()
 * @method MessageAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageAttachment::class);
    }

    // /**
    //  * @return MessageAttachment[] Returns an array of MessageAttachment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MessageAttachment
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
