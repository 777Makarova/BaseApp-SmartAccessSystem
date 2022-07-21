<?php


namespace App\Entity\Device;



use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'post', 'get'
    ],
    itemOperations: [
        'get',
        'put',
        'delete'
    ]
)]

#[ORM\Entity]
class DeviceUsingLog extends BaseEntity
{
    #[Assert\NotBlank]
    public string $User;


    #[ORM\Column(type: "boolean", nullable: true)]
    public bool $returned;

    #[ORM\Column(type: "datetime")]
    public \DateTimeInterface $pickUpDate;

    #[ORM\ManyToOne(targetEntity: Device::class)]
    private $Device;

    public function getDevice(): ?Device
    {
        return $this->Device;
    }

    public function setDevice(?Device $Device): self
    {
        $this->Device = $Device;

        return $this;
    }

}



