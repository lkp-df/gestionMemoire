<?php

namespace App\Repository;

use App\Entity\Encadreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Encadreur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Encadreur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Encadreur[]    findAll()
 * @method Encadreur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncadreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Encadreur::class);
    }

    // /**
    //  * @return Encadreur[] Returns an array of Encadreur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Encadreur
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
