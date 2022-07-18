<?php

namespace App\Entity\Admin;

use App\Entity\BaseEntity;
//use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ApiResource(
    collectionOperations: [
        'post',
        'get'
    ],
    itemOperations: [
        'get',
        'put',
        'delete'
    ]
)]

#[ORM\Entity()]
abstract class Admin extends BaseEntity implements PasswordHasherInterface, UserInterface
{
    #[ORM\Column(type: "string", unique: true, nullable: false)]
    public string $username;

    #[ORM\Column(type: 'json')]
    public array $roles = [];

    #[ORM\Column(type: 'string')]
    public string $password;

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getUserIdentifier(): string
    {
        return $this->username;
    }


    public function setUsername(string $userName)
    {
        $this->username = $userName;
    }

    public function getUsername():string
    {
        return $this->username;
    }

}