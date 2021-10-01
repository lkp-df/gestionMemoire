<?php

namespace App\Entity;

use App\Repository\EditEncadreurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EditEncadreurRepository::class)
 */
class EditEncadreur
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
    private $encadreur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEncadreur(): ?Encadreur
    {
        return $this->encadreur;
    }

    public function setEncadreur(Encadreur $encadreur): self
    {
        $this->encadreur = $encadreur;

        return $this;
    }
}
