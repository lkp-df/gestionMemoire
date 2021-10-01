<?php

namespace App\Repository;

use App\Entity\Soutenance;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


class SoutenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Soutenance::class);
    }


    public function findJuryToSoutenance(int $id)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT
            e.nom ,
            e.prenom,
            e.titre AS titre_enseignant,
            j.titre
        FROM
            App\Entity\Jury j
        JOIN j.encadreur e 
        JOIN j.soutenances s
         WHERE
            s.id = :id
            '
        )
            ->setParameter('id', $id);
        return $query->getResult();
    }

    public function listElevesToSoutenance($id)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT
                etu.nom AS nom_etu,
                etu.prenom As prenom_etu,
                mem.theme,
                mem.annee,
                f.codeFiliere,
                enca.nom As nom_encadreur,
                enca.prenom As prenom_encadreur,
                enca.titre,
                Et_Sout.noteSoutenance
            FROM App\Entity\Etudiant etu
            JOIN etu.filiere f
            JOIN etu.memoire mem
            JOIN etu.encadreur enca
            JOIN etu.etudiantSoutenances Et_Sout
            WHERE Et_Sout.soutenance = :id
            '
        )
            ->setParameter('id', $id);
        return $query->getResult();
    }


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
