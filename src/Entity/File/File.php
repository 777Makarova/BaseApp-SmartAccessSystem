<?php

namespace App\Entity\File;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\File\CreateFile;
use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'post' => [
            'method' => 'POST',
            'path' => '/files',
            'security' => 'is_granted("ROLE_USER")',
            'controller' => CreateFile::class,
            'defaults' => ['_api_receive'=> false],
        ],
        'get' => [
            'security' => 'is_granted("ROLE_ADMIN")',
        ]
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("ROLE_USER")']
    ],
    attributes: [
        'normalization_context' => ['groups' => ['GetFile', 'GetObjBase']],
        'denormalization_context' => ['groups' => ['SetFile']]
    ],
    cacheHeaders: [
        "max_age" => 240,
        "shared_max_age" => 480,
        "vary" => ["Authorization", "authorization", "Accept-Language"]
    ]
)]
#[ORM\Entity]
class File extends BaseEntity
{
    #[Groups(["SetFile"])]
    #[ApiProperty(attributes: ["openapi_context" => ["type" => "file"]])]
    public $file;

    #[ORM\Column(name: "path", type: "string", nullable: false)]
    #[Groups(["GetFile"])]
    public string $path;

    #[ORM\Column(type: "boolean", options: ["default" => 0])]
    #[Groups(["GetFile"])]
    public bool $isFullPath = false;
}
