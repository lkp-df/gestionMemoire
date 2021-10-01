<?php

namespace App\Repository;

use App\Entity\EditInfoEtudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditInfoEtudiant|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditInfoEtudiant|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditInfoEtudiant[]    findAll()
 * @method EditInfoEtudiant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditInfoEtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditInfoEtudiant::class);
    }

    // /**
    //  * @return EditInfoEtudiant[] Returns an array of EditInfoEtudiant objects
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
    public function findOneBySomeField($value): ?EditInfoEtudiant
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
