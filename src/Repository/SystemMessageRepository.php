<?php

namespace App\Repository;

use App\Entity\SystemMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SystemMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemMessage[]    findAll()
 * @method SystemMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemMessage::class);
    }

    // /**
    //  * @return SystemMessage[] Returns an array of SystemMessage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SystemMessage
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
