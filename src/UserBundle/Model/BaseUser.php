<?php


namespace App\UserBundle\Model;


use App\UserBundle\Util\Canonicalize;
use DateTime;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

abstract class BaseUser extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_DEFAULT     = 'ROLE_DEFAULT';

    public function __construct()
    {
        parent::__construct();
        $this->roles = [];
    }

    #[ORM\Column(type: "boolean")]
    #[Groups(["GetUser", "GetObjUser", "SetUser:admin"])]
    public bool $enabled = false;

    #[ORM\Column(type: "string", unique: true, nullable: false)]
    #[Groups(["GetUser", "GetObjUser", "SetUser"])]
    #[Assert\NotBlank]
    public string $username;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["GetUser", "GetObjUser", "SetUser"])]
    #[Assert\Email]
    public ?string $email;

    #[ORM\Column(type: "string", nullable: true)]
    #[Assert\Email]
    public ?string $emailCanonical;

    #[ORM\Column(type: "array")]
    #[Groups(["GetUser", "SetUser:admin"])]
    public array $roles;

    /**
     *Формат телефона E.164 - https://en.wikipedia.org/wiki/E.164.
     */
    #[ORM\Column(type: "string", nullable: true)]
    #[Assert\NotBlank(message: "user.registration.errors.phone_required", groups: ["SetUser"])]
    #[Assert\Regex("/[1-9]\d{1,14}$/")]
    #[Groups(["GetUser", "GetObjUser", "SetUser",])]
    public ?string $phone = null;

    #[ORM\Column(type: "boolean", options: ["default"=>0])]
    #[Groups(["GetUser"])]
    public bool $isExternalUser = false;

    #[ORM\Column(type: "boolean", options: ["default"=>0])]
    #[Groups(["GetUser"])]
    public bool $isEmailConfirmed = false;

    #[ORM\Column(type: "boolean", options: ["default"=>0])]
    #[Groups(["GetUser"])]
    public bool $isPhoneConfirmed = false;

    /**
     * The salt to use for hashing.
     */
    protected ?string $salt = null;

    #[ORM\Column(type: "string", nullable: false)]
    #[Assert\Length(min: 5, minMessage: "min 5 symbols")]
    protected string $password;

    #[ApiProperty(writable: true)]
    #[Assert\NotBlank(groups: ["SetUser"])]
    #[Groups(["SetUser"])]
    public string $plainPassword;

    #[ORM\Column(type: "string", nullable: false)]
    protected string $usernameCanonical;

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function setEmailCanonical()
    {
        $this->emailCanonical = Canonicalize::canonicalize($this->email);
    }

    public function setUsernameCanonical()
    {
        $this->usernameCanonical = Canonicalize::canonicalize($this->username);
    }

    public function getUsernameCanonical():string
    {
        return $this->usernameCanonical;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function setRolesRaw(array $roles)
    {
//        $this->roles = [];
//
//
//        foreach ($roles as $role) {
//            $this->roles[] = strtoupper($role);
//        }
//        $this->roles = array_unique($this->roles);
//        $this->roles = array_values($this->roles);
//
//        return $this;


        $this->roles = $roles;

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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function getGroups():array
    {
        return [];
    }

    public function getGroupNames():array
    {
        return [];
    }

    public function getRoles():array
    {
//        $roles = $this->roles;
//
//        foreach ($this->getGroups() as $group) {
//            $roles = array_merge($roles, $group->getRoles());
//        }
//
//        // we need to make sure to have at least one role
//        $roles[] = static::ROLE_DEFAULT;
//
//        return array_values(array_unique($roles));

        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles):static
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string $role):static
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role):static
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole($role = null): bool
    {
        if (is_null($role)) {
            return false;
        }
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function isSuperAdmin():bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function hasGroup($name = null): bool
    {
        if (is_null($name)) {
            return true;
        }
        return in_array($name, $this->getGroupNames());
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
