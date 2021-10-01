<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtudiantRepository::class)
 */
class Etudiant
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\ManyToMany(targetEntity=Soutenance::class, inversedBy="etudiants")
     */
    private $soutenance;

    /**
     * @ORM\ManyToOne(targetEntity=Filiere::class, inversedBy="etudiants")
     */
    private $filiere;

    /**
     * @ORM\ManyToOne(targetEntity=Memoire::class, inversedBy="etudiants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $memoire;

    /**
     * @ORM\ManyToOne(targetEntity=Encadreur::class, inversedBy="etudiants")
     */
    private $encadreur;

    /**
     * @ORM\OneToMany(targetEntity=EtudiantSoutenances::class, mappedBy="etudiant")
     */
    private $etudiantSoutenances;
    
    public function __construct()
    {
        $this->soutenance = new ArrayCollection();
        $this->etudiantSoutenances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection|Soutenance[]
     */
    public function getSoutenance(): Collection
    {
        return $this->soutenance;
    }

    public function addSoutenance(Soutenance $soutenance): self
    {
        if (!$this->soutenance->contains($soutenance)) {
            $this->soutenance[] = $soutenance;
        }

        return $this;
    }

    public function removeSoutenance(Soutenance $soutenance): self
    {
        $this->soutenance->removeElement($soutenance);

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getMemoire(): ?Memoire
    {
        return $this->memoire;
    }

    public function setMemoire(?Memoire $memoire): self
    {
        $this->memoire = $memoire;

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
            $etudiantSoutenance->setEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantSoutenance(EtudiantSoutenances $etudiantSoutenance): self
    {
        if ($this->etudiantSoutenances->removeElement($etudiantSoutenance)) {
            // set the owning side to null (unless already changed)
            if ($etudiantSoutenance->getEtudiant() === $this) {
                $etudiantSoutenance->setEtudiant(null);
            }
        }

        return $this;
    }

}
