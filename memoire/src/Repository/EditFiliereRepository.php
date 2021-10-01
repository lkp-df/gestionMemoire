<?php

namespace App\Repository;

use App\Entity\EditFiliere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditFiliere|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditFiliere|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditFiliere[]    findAll()
 * @method EditFiliere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditFiliereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditFiliere::class);
    }

    // /**
    //  * @return EditFiliere[] Returns an array of EditFiliere objects
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
    public function findOneBySomeField($value): ?EditFiliere
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
