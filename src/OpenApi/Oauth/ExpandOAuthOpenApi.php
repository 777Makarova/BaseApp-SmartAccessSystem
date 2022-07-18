<?php

namespace App\OpenApi\Oauth;

use ApiPlatform\Core\OpenApi\Model\MediaType;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\OpenApi\ExpandOpenApiInterface;
use ArrayObject;

class ExpandOAuthOpenApi implements ExpandOpenApiInterface
{
    private OpenApi $openApi;

    public function apply(OpenApi $openApi): void
    {
        $this->openApi = $openApi;

        $this
            ->applyAuthToken()
        ;
    }

    private function applyAuthToken()
    {
        $pathItem = new PathItem();

        $pathItem = $pathItem->withPost(
            (new Operation('webant_oauth_token', ['Authorization']))->withSummary('Получение токена')
                ->withRequestBody(
                    (new RequestBody())
                        ->withContent(
                            new ArrayObject([
                                'multipart/form-data' => new MediaType(new ArrayObject([
                                    'type' => 'object',
                                    'properties' => [
                                        'username' => [
                                            'type' => 'string',
                                        ],
                                        'password' => [
                                            'type' => 'string',
                                        ],
                                        'client_id' => [
                                            'type' => 'string',
                                        ],
                                        'client_secret' => [
                                            'type' => 'string',
                                        ],
                                        'grant_type' => [
                                            'type' => 'string',
                                            'enum' => ['client_credentials', 'password', 'code', 'token', 'refresh_token']
                                        ],
                                        'urn:social:auth' => [
                                            'type' => 'string',
                                            'description' => 'Тип внешнего сервиса для авторизации (не передавать при стандартной авторизации)',
                                        ],
                                        'code' => [
                                            'type' => 'string',
                                            'description' => 'Код, полученный от внешнего сервиса (не передавать при стандартной авторизации)',
                                        ],
                                    ],
                                ])),
                            ])
                        )
                )
                ->withResponses([
                    200 => new Response('', new ArrayObject([
                        'application/json' => new MediaType(
                            new ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'access_token' => ['type' => 'string'],
                                    'refresh_token' => ['type' => 'string'],
                                    'token_type' => ['type' => 'string'],
                                    'scope' => ['type' => 'string', 'value' => null],
                                    'expired_at' => ['type' => 'integer'],
                                ],
                            ])
                        ),
                    ])),
                    400 => new Response('', new ArrayObject([
                        'application/json' => new MediaType(
                            new ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'error' => ['type' => 'string'],
                                    'error_description' => ['type' => 'string'],
                                ],
                            ])
                        ),
                    ])),
                ])
        );

        $this->openApi->getPaths()->addPath('/token', $pathItem);
    }
}
