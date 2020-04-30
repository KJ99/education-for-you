<?php

namespace App\Repository;

use App\Entity\GroupInviteToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GroupInviteToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupInviteToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupInviteToken[]    findAll()
 * @method GroupInviteToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupInviteTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupInviteToken::class);
    }

    // /**
    //  * @return GroupInviteToken[] Returns an array of GroupInviteToken objects
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
    public function findOneBySomeField($value): ?GroupInviteToken
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
