<?php

namespace App\Entity;

use App\Repository\CompletSoutenanceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompletSoutenanceRepository::class)
 */
class CompletSoutenance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeSoutenance;

    /**
     * @ORM\Column(type="date")
     */
    private $date;
   
    /**
     * @ORM\Column(type="float")
     */
    private $note;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(string $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function getTypeSoutenance(): ?string
    {
        return $this->typeSoutenance;
    }

    public function setTypeSoutenance(string $typeSoutenance): self
    {
        $this->typeSoutenance = $typeSoutenance;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

        return $this;
    }
}
