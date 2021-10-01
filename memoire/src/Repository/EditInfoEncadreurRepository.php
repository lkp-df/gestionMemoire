<?php

namespace App\Repository;

use App\Entity\EditInfoEncadreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditInfoEncadreur|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditInfoEncadreur|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditInfoEncadreur[]    findAll()
 * @method EditInfoEncadreur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditInfoEncadreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditInfoEncadreur::class);
    }

    // /**
    //  * @return EditInfoEncadreur[] Returns an array of EditInfoEncadreur objects
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
    public function findOneBySomeField($value): ?EditInfoEncadreur
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
