<?php

namespace App\Repository;

use App\Entity\EtudiantSoutenances;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EtudiantSoutenances|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtudiantSoutenances|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtudiantSoutenances[]    findAll()
 * @method EtudiantSoutenances[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudiantSoutenancesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtudiantSoutenances::class);
    }

    // /**
    //  * @return EtudiantSoutenances[] Returns an array of EtudiantSoutenances objects
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
    public function findOneBySomeField($value): ?EtudiantSoutenances
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
