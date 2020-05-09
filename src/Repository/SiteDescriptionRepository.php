<?php

namespace App\Repository;

use App\Entity\SiteDescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SiteDescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiteDescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiteDescription[]    findAll()
 * @method SiteDescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteDescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteDescription::class);
    }

    // /**
    //  * @return SiteDescription[] Returns an array of SiteDescription objects
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
    public function findOneBySomeField($value): ?SiteDescription
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
