<?php

namespace App\Repository;

use App\Entity\GroupResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GroupResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupResource[]    findAll()
 * @method GroupResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupResource::class);
    }

    // /**
    //  * @return GroupResource[] Returns an array of GroupResource objects
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
    public function findOneBySomeField($value): ?GroupResource
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
