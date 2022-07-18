<?php


namespace App\Entity\Group;


use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\BaseEntity;
use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

class Group extends BaseEntity
{

    public const ROLE_GROUP_GET = 'ROLE_GROUP_GET';
    public const ROLE_GROUP_CREATE = 'ROLE_GROUP_CREATE';
    public const ROLE_GROUP_UPDATE = 'ROLE_GROUP_UPDATE';
    public const ROLE_GROUP_DELETE = 'ROLE_GROUP_DELETE';
    public const ROLE_GROUP_USER_ADD = 'ROLE_GROUP_USER_ADD';

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Groups({"GetGroup", "GetObjGroup", "SetGroup", "GetGroupPreview"})
     */
    protected string $name;

    /**
     * @ORM\Column(type="array")
     * @Groups({"GetGroup", "GetObjGroup", "SetGroup"})
     */
    protected array $roles;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"GetGroup", "GetObjGroup", "SetGroup", "GetGroupPreview"})
     */
    public ?string $localization = null;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     * @Groups({"GetGroup", "GetObjGroup", "SetGroup", "GetGroupPreview"})
     */
    public bool $isDefault = false;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     * @Groups({"GetGroup", "GetObjGroup", "SetGroup", "GetGroupPreview"})
     */
    public bool $isDeletable = true;


    /**
     * @ORM\ManyToMany(targetEntity="App\UserBundle\Entity\User", inversedBy="groups", cascade={"persist"})
     * @ApiSubresource(maxDepth=1)
     * @Groups({"UpdateGroup", "SetGroup"})
     */
    #[ApiProperty(security: 'is_granted("ROLE_GROUP_USER_ADD")')]
    public iterable $users;

    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addGroup($this);
        }
    }

    public function removeUser(User $user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeGroup($this);
        }
    }

    public function clearUser()
    {
        $this->users->clear();
    }

    public function __construct(string $name = '', $roles = [])
    {
        parent::__construct($name, $roles);
        $this->users = new ArrayCollection();
    }

    public static function groupNames(): array
    {
        return [
            self::ROLE_GROUP_GET => 'roles.group.get',
            self::ROLE_GROUP_CREATE => 'roles.group.create',
            self::ROLE_GROUP_UPDATE => 'roles.group.update',
            self::ROLE_GROUP_DELETE => 'roles.group.delete',
            self::ROLE_GROUP_USER_ADD => 'roles.group.user_add',
        ];
    }



}
