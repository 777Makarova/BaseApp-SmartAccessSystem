<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\User\ActionCheckEmail;
use App\Controller\User\ActionCheckPhone;
use App\Repository\User\UserRepository;
use App\UserBundle\Controller\CreateUser;
use App\UserBundle\Controller\CurrentUser;
use App\UserBundle\Controller\UpdateUser;
use App\UserBundle\Model\BaseUser;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        "get" => [
            "normalization_context" => ["groups" => ["GetUser", "GetObjUser"]],
            "security" => "is_granted('ROLE_ADMIN')",
        ],
        "post" => [
            "normalization_context" => ["groups" => ["GetUser", "GetObjUser"]],
            "denormalization_context" => ["groups" => ["SetUser", "SetObjUser"]],
            "controller" => CreateUser::class,
        ],
        "current" => [
            "method" => "GET",
            "path" => "/users/current",
            "controller" => CurrentUser::class,
            "pagination_enabled" => false,
            "security" => "is_granted('ROLE_USER')",
            "normalization_context" => ["groups" => ["GetUser", "GetObjUser"]],
        ],
        "check_email" => [
            "method" => "patch",
            "path" => "/users/check-email",
            "controller" => ActionCheckEmail::class,
            "pagination_enabled" => false,
            "defaults" => ["_api_receive" => false],
        ],
        "check_phone" => [
            "method" => "patch",
            "path" => "/users/check-phone",
            "controller" => ActionCheckPhone::class,
        "pagination_enabled" => false,
            "defaults" => ["_api_receive" => false],
        ]
    ],
    itemOperations: [
        "get" => [
            "security" => "is_granted('use_service', object) and is_granted('can_edit', object)",
            "normalization_context" => ["groups" => ["GetUser", "GetObjUser"]],
            "denormalization_context" => ["groups" => ["SetUser", "SetObjUser"]],
        ],
        "put" => [
            "security" => "is_granted('can_edit', object)",
            "normalization_context"  => ["groups" => ["GetUser", "GetObjUser"]],
            "denormalization_context" => ["groups" => ["SetUser"]],
            "controller" => UpdateUser::class
        ],
        "delete" => [
            "security" => "is_granted('ROLE_USER_DELETE')",
        ]
    ]
)]
#[ApiFilter(
    SearchFilter::class, properties: [
      "usename" => "partial",
      "email" => "partial",
      "roles" => "partial",
      "phone" => "partial",
    ]
)]
 
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "user")]
#[UniqueEntity("username")]
#[UniqueEntity("phone")]
#[UniqueEntity("email")]
class User extends BaseUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Groups(["GetUser","GetObjUser"])]
    protected int $id;

    #[ORM\Column(type: "boolean", options: ["default"=>0])]
    #[Groups(["GetUser", "SetUser"])]
    public bool $isAgreementAccepted = false;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["GetUser", "GetObjUser", "SetUser"])]
    public ?string $firstName;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["GetUser", "GetObjUser", "SetUser"])]
    public ?string $lastName;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["GetUser", "GetObjUser", "SetUser"])]
    public ?string $middleName;

    #[Groups(["GetUser", "GetObjUser",])]
    private ?string $fullName;

    #[ORM\Column(type: "boolean", options: ["default"=>0])]
    #[Groups(["GetUser",])]
    public bool $isRealEmail = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?DateTime $lastConfirmCodeDate;

    /**
     * email || phone.
     */
    #[ApiProperty(attributes: [
        "swagger_context"=> [
            "type" => "string",
            "enum" => ["email", "sms"],
            "example" => "email"
        ]
    ])]
    #[Groups(["setUser"])]
    public string $activationType = 'sms';

    public function getFullName(): ?string
    {
        return sprintf('%s %s %s', $this->firstName, $this->middleName ? $this->middleName.' ' : '', $this->lastName);
    }

    public function isEqualTo(UserInterface $user = null): bool
    {
        if (is_null($user)) {
            return false;
        }

        return $user->getUsername() === $this->username;
    }



}
