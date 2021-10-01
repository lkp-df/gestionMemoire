<?php

namespace App\Repository;

use App\Entity\EditEncadreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditEncadreur|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditEncadreur|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditEncadreur[]    findAll()
 * @method EditEncadreur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditEncadreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditEncadreur::class);
    }

    // /**
    //  * @return EditEncadreur[] Returns an array of EditEncadreur objects
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
    public function findOneBySomeField($value): ?EditEncadreur
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
