<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SoutenanceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=SoutenanceRepository::class)
 */
class Soutenance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $salle;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $type;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=Jury::class, mappedBy="soutenances")
     */
    private $juries;

    /**
     * @ORM\ManyToMany(targetEntity=Etudiant::class, mappedBy="soutenance")
     */
    private $etudiants;

    /**
     * @ORM\OneToMany(targetEntity=EtudiantSoutenances::class, mappedBy="soutenance")
     */
    private $etudiantSoutenances;

    public function __construct()
    {
        $this->juries = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
        $this->etudiantSoutenances = new ArrayCollection();
    }

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Jury[]
     */
    public function getJuries(): Collection
    {
        return $this->juries;
    }

    public function addJury(Jury $jury): self
    {
        if (!$this->juries->contains($jury)) {
            $this->juries[] = $jury;
            $jury->setSoutenances($this);
        }

        return $this;
    }

    public function removeJury(Jury $jury): self
    {
        if ($this->juries->removeElement($jury)) {
            // set the owning side to null (unless already changed)
            if ($jury->getSoutenances() === $this) {
                $jury->setSoutenances(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Etudiant[]
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): self
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
            $etudiant->addSoutenance($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->removeElement($etudiant)) {
            $etudiant->removeSoutenance($this);
        }

        return $this;
    }

    /**
     * @return Collection|EtudiantSoutenances[]
     */
    public function getEtudiantSoutenances(): Collection
    {
        return $this->etudiantSoutenances;
    }

    public function addEtudiantSoutenance(EtudiantSoutenances $etudiantSoutenance): self
    {
        if (!$this->etudiantSoutenances->contains($etudiantSoutenance)) {
            $this->etudiantSoutenances[] = $etudiantSoutenance;
            $etudiantSoutenance->setSoutenance($this);
        }

        return $this;
    }

    public function removeEtudiantSoutenance(EtudiantSoutenances $etudiantSoutenance): self
    {
        if ($this->etudiantSoutenances->removeElement($etudiantSoutenance)) {
            // set the owning side to null (unless already changed)
            if ($etudiantSoutenance->getSoutenance() === $this) {
                $etudiantSoutenance->setSoutenance(null);
            }
        }

        return $this;
    }
}
