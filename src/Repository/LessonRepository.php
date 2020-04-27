<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\Unit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function incrementHigherOrderNumbers(int $number, Unit $unit) {
        return $this->createQueryBuilder('l')
            ->update()
            ->set('l.weight', 'l.weight + 1')
            ->andWhere('l.weight >= :number')
            ->andWhere('l.unit = :unit')
            ->setParameter('number', $number)
            ->setParameter('unit', $unit)
            ->getQuery()
            ->execute();
    }

    public function totalCount() {
        return $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countForUnit(Unit $unit) {
        return $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->andWhere('l.unit = :unit')
            ->setParameter('unit', $unit)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Lesson[] Returns an array of Lesson objects
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
    public function findOneBySomeField($value): ?Lesson
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
