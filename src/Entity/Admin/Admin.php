<?php

namespace App\Entity\Admin;



use App\Controller\Admin\CreateAdmin;
use App\Entity\BaseEntity;
use App\UserBundle\Model\BaseAdmin;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'post'=>[
            'controller' => CreateAdmin::class,
//            "security" => "is_granted('ROLE_ADMIN')"

        ],
        'get'
    ],
    itemOperations: [
        'get',
        'put',
        'delete'
    ]
)]

#[ORM\Entity()]
class Admin extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: "string", unique: true, nullable: false)]
//    #[Groups(["GetUser", "GetObjUser", "SetUser"])]
    #[Assert\NotBlank]
    public string $username;


    #[ORM\Column(type: "array")]
//    #[Groups(["GetUser", "SetUser:admin"])]
    public array $roles;

    /**
     * The salt to use for hashing.
     */
    protected ?string $salt = null;

    #[ORM\Column(type: "string", nullable: false)]
    #[Assert\Length(min: 5, minMessage: "min 5 symbols")]
    protected string $password;



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


    public function getSalt(): ?string
    {
        return $this->salt;
    }









}