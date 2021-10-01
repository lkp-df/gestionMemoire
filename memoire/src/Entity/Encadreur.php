<?php

namespace App\Entity;

use App\Repository\EncadreurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=EncadreurRepository::class)
 */
class Encadreur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="ce champ ne peut pas etre vide")
     * 
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="ce champ ne peut pas etre vide")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ ne peut pas etre vide")
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Il faut impérativement une image")
     * @Assert\File(
     * maxSize = "2M",  
     * maxSizeMessage = "la Taillle maximum des fichiers est de 2 mega Octets",
     * mimeTypes = {"image/png","image/jpeg","image/jpg"},
     * mimeTypesMessage = "Les extensions autorisées sont png, jpeg, jpg")
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity=Jury::class, mappedBy="encadreur")
     */
    private $juries;

    /**
     * @ORM\OneToMany(targetEntity=Etudiant::class, mappedBy="encadreur")
     */
    private $etudiants;

    public function __construct()
    {
        $this->juries = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }


    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

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
            $jury->setEncadreur($this);
        }

        return $this;
    }

    public function removeJury(Jury $jury): self
    {
        if ($this->juries->removeElement($jury)) {
            // set the owning side to null (unless already changed)
            if ($jury->getEncadreur() === $this) {
                $jury->setEncadreur(null);
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
            $etudiant->setEncadreur($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getEncadreur() === $this) {
                $etudiant->setEncadreur(null);
            }
        }

        return $this;
    }
}
