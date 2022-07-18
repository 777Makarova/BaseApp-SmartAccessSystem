<?php

namespace App\Entity\Code;

use App\Entity\BaseEntity;
use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Sms extends BaseEntity
{
    #[ORM\Column(type: "string", nullable: false)]
    public string $phone;

    #[ORM\Column(type: "string", nullable: false)]
    public string $message;

    #[ORM\Column(type: "string", nullable: false)]
    public string $status;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?User $user;
}
