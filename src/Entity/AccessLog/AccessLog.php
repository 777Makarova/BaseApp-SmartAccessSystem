<?php

namespace App\Entity\AccessLog;


use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\TokenJWT\CheckJWTController;

#[ApiResource (
    collectionOperations: [
        'post'=>[
            'deserialize' => false,
            'controller' => CheckJWTController::class,
            'path'=>'/checkJWT',
            'openapi_context' =>[
                'requestBody' =>[
                    'description' => 'Check JWT Token',
                    'required' => true,
                    'content'=>[
                        'multipart/form-data'=>[
                            'schema'=>[
                                'type' => 'object',
                                'properties' => [
                                    'JWT' => [
                                        'type' => 'string',
                                        'description' => 'Write the JWT'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    itemOperations: ['get'=>[
        'path'=>'checkJWT'
    ]]

)]
#[ORM\Entity]
class AccessLog extends BaseEntity
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    public string $user_id_byClaim;

    /**
     * @var array
     */
    #[ORM\Column(type: 'json')]
    public array $roles_byClaim;

    #[ORM\Column(type: 'text', )]
    public string $token;


    #[ORM\Column(type: 'string')]
    public string $result;
}