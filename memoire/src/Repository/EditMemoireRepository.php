<?php

namespace App\Repository;

use App\Entity\EditMemoire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditMemoire|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditMemoire|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditMemoire[]    findAll()
 * @method EditMemoire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditMemoireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditMemoire::class);
    }

    // /**
    //  * @return EditMemoire[] Returns an array of EditMemoire objects
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
    public function findOneBySomeField($value): ?EditMemoire
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
