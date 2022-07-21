<?php

namespace App\Entity\TokenJWT;


use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\TokenJWT\CreateJWTController;
use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
#[ApiResource (
    collectionOperations: [
        'GetToken'=>[
            'method'=> 'POST',
            'path' => '/getToken',
            'deserialize' => false,
            'controller' => CreateJWTController::class,
            'openapi_context' =>[
                'requestBody' =>[
                    'description' => 'Выдать токен',
                    'required' => true,
                    'content'=>[
                        'multipart/form-data'=>[
                            'schema'=>[
                                'type' => 'object',
                                'properties' => [
                                    'user_id' => [
                                        'type' => 'string',
                                        'description' => 'Введите user_id'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'CheckToken'=>[
            'method'=> 'POST',
            'path' => '/checkToken',
            'deserialize' => false,
            'controller' => CreateJWTController::class,
            'openapi_context' =>[
                'requestBody' =>[
                    'description' => 'Проверить токен',
                    'required' => true,
                    'content'=>[
                        'multipart/form-data'=>[
                            'schema'=>[
                                'type' => 'object',
                                'properties' => [
                                    'JWT' => [
                                        'type' => 'string',
                                        'description' => 'Введите токен'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],

    ],
    itemOperations: ['get', 'delete']

)]

class Token extends BaseEntity
{
    #[ORM\Column(type:'string')]
    #[Assert\NotNull]
    public string $user_id;

    #[ORM\Column(type:'string', length: '16777215')]
    #[Assert\NotNull]
    public string $token;

}