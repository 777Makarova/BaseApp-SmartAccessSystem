<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

abstract class BaseEntity
{
    use TimestampableEntityTrait;

    public function __construct()
    {
        $this->initDates();
    }

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Groups(["GetBase","GetObjBase"])]
    protected int $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
