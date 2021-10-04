<?php

namespace App\Repository;

use App\Entity\EditUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditUser[]    findAll()
 * @method EditUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditUser::class);
    }

    // /**
    //  * @return EditUser[] Returns an array of EditUser objects
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
    public function findOneBySomeField($value): ?EditUser
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
