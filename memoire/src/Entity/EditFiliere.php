<?php

namespace App\Entity;

use App\Repository\EditFiliereRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EditFiliereRepository::class)
 */
class EditFiliere
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="object", length=255)
     */
    private $filiere;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(Filiere $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }
}
