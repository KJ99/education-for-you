<?php

namespace App\Repository;

use App\Entity\Unit;
use App\Entity\Level;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Unit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Unit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Unit[]    findAll()
 * @method Unit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unit::class);
    }

    public function incrementHigherOrderNumbers(int $number, Level $level) {
        return $this->createQueryBuilder('u')
            ->update()
            ->set('u.weight', 'u.weight + 1')
            ->andWhere('u.weight >= :number')
            ->andWhere('u.level = :level')
            ->setParameter('number', $number)
            ->setParameter('level', $level)
            ->getQuery()
            ->execute();
    }

    public function totalCount() {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countForLevel(Level $level) {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.level = :level')
            ->setParameter('level', $level)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Unit[] Returns an array of Unit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Unit
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
