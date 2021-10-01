<?php

namespace App\Entity;

use App\Repository\JuryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JuryRepository::class)
 */
class Jury
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
    private $titre;

    /**
     * @ORM\ManyToOne(targetEntity=Soutenance::class, inversedBy="juries")
     */
    private $soutenances;

    /**
     * @ORM\ManyToOne(targetEntity=Encadreur::class, inversedBy="juries")
     */
    private $encadreur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSoutenances(): ?Soutenance
    {
        return $this->soutenances;
    }

    public function setSoutenances(?Soutenance $soutenances): self
    {
        $this->soutenances = $soutenances;

        return $this;
    }

    public function getEncadreur(): ?Encadreur
    {
        return $this->encadreur;
    }

    public function setEncadreur(?Encadreur $encadreur): self
    {
        $this->encadreur = $encadreur;

        return $this;
    }
}
