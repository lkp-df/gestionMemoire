<?php

namespace App\Entity;

use App\Repository\EtudiantSoutenancesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtudiantSoutenancesRepository::class)
 */
class EtudiantSoutenances
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Etudiant::class, inversedBy="etudiantSoutenances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etudiant;

    /**
     * @ORM\ManyToOne(targetEntity=Soutenance::class, inversedBy="etudiantSoutenances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $soutenance;

    /**
     * @ORM\Column(type="float")
     */
    private $noteSoutenance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getSoutenance(): ?Soutenance
    {
        return $this->soutenance;
    }

    public function setSoutenance(?Soutenance $soutenance): self
    {
        $this->soutenance = $soutenance;

        return $this;
    }

    public function getNoteSoutenance(): ?float
    {
        return $this->noteSoutenance;
    }

    public function setNoteSoutenance(float $noteSoutenance): self
    {
        $this->noteSoutenance = $noteSoutenance;

        return $this;
    }
}
