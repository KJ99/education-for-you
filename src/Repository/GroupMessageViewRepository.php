<?php

namespace App\Repository;

use App\Entity\GroupMessageView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GroupMessageView|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupMessageView|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupMessageView[]    findAll()
 * @method GroupMessageView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupMessageViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMessageView::class);
    }

    // /**
    //  * @return GroupMessageView[] Returns an array of GroupMessageView objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupMessageView
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
