<?php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FiliereRepository::class)
 */
class Filiere
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=6)
     * @Assert\NotBlank
     * @Assert\Length(
     * min=3,
     * minMessage="Il faut 3 caractères",
     * max=6,
     * maxMessage="juste 6 caractères sont requis"
     * )
     */
    private $codeFiliere;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $designation;


    /**
     * @ORM\OneToMany(targetEntity=Etudiant::class, mappedBy="filiere")
     */
    private $etudiants;


    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeFiliere(): ?string
    {
        return $this->codeFiliere;
    }

    public function setCodeFiliere(string $codeFiliere): self
    {
        $this->codeFiliere = $codeFiliere;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

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
            $etudiant->setFiliere($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getFiliere() === $this) {
                $etudiant->setFiliere(null);
            }
        }

        return $this;
    }
}
