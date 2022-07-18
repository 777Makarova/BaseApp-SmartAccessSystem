<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait TimestampableEntityTrait
{
    #[ORM\Column(type: "datetime")]
    #[Groups(["GetBase", "GetObjBase"])]
    protected \DateTimeInterface $dateCreate;

    #[ORM\Column(type: "datetime")]
    #[Groups(["GetBase", "GetObjBase"])]
    protected \DateTimeInterface $dateUpdate;

    /**
     * this method should be called from constructor.
     */
    private function initDates()
    {
        try {
            $this->dateCreate = new DateTimeImmutable();
            $this->dateUpdate = new DateTimeImmutable();
        } catch (\Exception $e) {
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    #[ORM\PrePersist]
    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        try {
            $now = new DateTimeImmutable();
            $this->dateUpdate = $now;
        } catch (\Exception $e) {
        }
    }
}
