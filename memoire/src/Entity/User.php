<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 * fields={"email"},
 * message="cet email existe déja, veuillez changer svp"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Email(
     * message="l'email saisi n'est pas valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *  message="veuillez remplir ce champ svp")
     * @Assert\Length(min=8,minMessage="Il faut au moins 8 caracteres pour un mot de passe")
     * @Assert\EqualTo(
     * propertyPath="confirmPassword",
     * message="Veuillez saisir un mot de passe identique svp")
     */
    private $password;

    /**
     * @Assert\NotBlank
     * @Assert\EqualTo(
     * propertyPath="password",
     * message="Veuillez saisir un mot de passe identique svp"
     * )
     */
    private $confirmPassword;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    
    /**
     * Get propertyPath="password",
     */ 
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * Set propertyPath="password",
     *
     * @return  self
     */ 
    public function setConfirmPassword($confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    //implementations des methodes de userInteface
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
    }
    public function eraseCredentials()
    {
    }
}
