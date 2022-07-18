<?php

namespace App\UserBundle\Model;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\BaseEntity;
use App\UserBundle\Util\Canonicalize;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class BaseAdmin extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_DEFAULT     = 'ROLE_DEFAULT';

    public function __construct()
    {
        parent::__construct();
        $this->roles = [];
    }

//    #[ORM\Column(type: "boolean")]
//    #[Groups(["GetUser", "GetObjUser", "SetUser:admin"])]
//    public bool $enabled = false;

    #[ORM\Column(type: "string", unique: true, nullable: false)]
    #[Groups(["GetUser", "GetObjUser", "SetUser"])]
    #[Assert\NotBlank]
    public string $username;


    #[ORM\Column(type: "array")]
    #[Groups(["GetUser", "SetUser:admin"])]
    public array $roles;

    /**
     * The salt to use for hashing.
     */
    protected ?string $salt = null;

    #[ORM\Column(type: "string", nullable: false)]
    #[Assert\Length(min: 5, minMessage: "min 5 symbols")]
    protected string $password;

//    #[ApiProperty(writable: true)]
//    #[Assert\NotBlank(groups: ["SetUser"])]
//    #[Groups(["SetUser"])]
//    public string $plainPassword;

//    #[ORM\Column(type: "string", nullable: false)]
//    protected string $usernameCanonical;

    public function getUserIdentifier(): string
    {
        return $this->username;
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



    public function setUsername(string $userName)
    {
        $this->username = $userName;
    }

    public function getUsername():string
    {
        return $this->username;
    }


//    public function getPlainPassword(): string
//    {
//        return $this->plainPassword;
//    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    public function getRoles():array
    {

        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }





    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}