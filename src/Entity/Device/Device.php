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
class Device extends BaseEntity
{
    #[Assert\NotBlank]
    public string $OSVersion;


    #[ORM\Column(type: "boolean")]
    public bool $takenАway;

    #[ORM\Column(type: "boolean", nullable: true)]
    public bool $returnedIn24Hours;


}



