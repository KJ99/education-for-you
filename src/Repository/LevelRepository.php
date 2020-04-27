<?php

namespace App\Repository;

use App\Entity\Level;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Level|null find($id, $lockMode = null, $lockVersion = null)
 * @method Level|null findOneBy(array $criteria, array $orderBy = null)
 * @method Level[]    findAll()
 * @method Level[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Level::class);
    }

    public function incrementHigherOrderNumbers(int $number, Subject $subject) {
        return $this->createQueryBuilder('l')
            ->update()
            ->set('l.weight', 'l.weight + 1')
            ->andWhere('l.weight >= :number')
            ->andWhere('l.subject = :subject')
            ->setParameter('number', $number)
            ->setParameter('subject', $subject)
            ->getQuery()
            ->execute();
    }

    public function totalCount() {
        return $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countForSubject(Subject $subject) {
        return $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->andWhere('l.subject = :subject')
            ->setParameter('subject', $subject)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Level[] Returns an array of Level objects
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
    public function findOneBySomeField($value): ?Level
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
