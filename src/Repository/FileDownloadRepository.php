<?php

namespace App\Repository;

use App\Entity\FileDownload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FileDownload|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileDownload|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileDownload[]    findAll()
 * @method FileDownload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileDownloadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileDownload::class);
    }

    // /**
    //  * @return FileDownload[] Returns an array of FileDownload objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FileDownload
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
