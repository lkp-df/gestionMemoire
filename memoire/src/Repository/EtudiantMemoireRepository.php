<?php

namespace App\Repository;

use App\Entity\EtudiantMemoire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EtudiantMemoire|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtudiantMemoire|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtudiantMemoire[]    findAll()
 * @method EtudiantMemoire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudiantMemoireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtudiantMemoire::class);
    }

    // /**
    //  * @return EtudiantMemoire[] Returns an array of EtudiantMemoire objects
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
    public function findOneBySomeField($value): ?EtudiantMemoire
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
