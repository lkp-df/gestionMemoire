<?php

namespace App\Repository;

use App\Entity\CompletSoutenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompletSoutenance|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompletSoutenance|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompletSoutenance[]    findAll()
 * @method CompletSoutenance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompletSoutenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompletSoutenance::class);
    }

    // /**
    //  * @return CompletSoutenance[] Returns an array of CompletSoutenance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompletSoutenance
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
