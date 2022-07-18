<?php

namespace App\OpenApi\User;

use ApiPlatform\Core\OpenApi\Model\MediaType;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\OpenApi\ExpandOpenApiInterface;
use ArrayObject;

class ExpandUserOpenApi implements ExpandOpenApiInterface
{
    private OpenApi $openApi;

    public function apply(OpenApi $openApi): void
    {
        $this->openApi = $openApi;

        $this
            ->applyCheckEmail()
            ->applyCheckPhone()
        ;
    }

    private function applyCheckEmail(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/users/check-email');
        $schemas = $this->openApi->getComponents()->getSchemas();

        $schemas['Email'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => $schemas['User']['properties']['email']
                ,
            ],
        ]);

        $operation = $pathItem->getPatch();

        $this->openApi->getPaths()->addPath('/users/check-email', $pathItem->withPatch(
            $operation
                ->withSummary('Проверка email')
                ->withParameters([])
                ->withRequestBody(new RequestBody(
                    'Проверка email',
                    new ArrayObject([
                        'application/json' => new MediaType(
                            new ArrayObject([
                                '$ref' => '#/components/schemas/Email',
                            ])
                        ),
                    ])
                ))
                ->withResponses([
                    200 => new Response('', new ArrayObject([
                        'application/json' => new MediaType(
                            null,
                            ['message' => 'ok']
                        ),
                    ])),
                    429 => [],
                    409 => [],
                    400 => [],
                ])
        )->withDescription('Проверка email'));

        return $this;
    }

    private function applyCheckPhone(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/users/check-phone');
        $operation = $pathItem->getPatch();

        $schemas = $this->openApi->getComponents()->getSchemas();

        $schemas['Phone'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'phone' => $schemas['User']['properties']['phone']
                ,
            ],
        ]);
        $this->openApi->getPaths()->addPath('/users/check-phone', $pathItem->withPatch(
            $operation
                ->withSummary('Проверка телефона')
                ->withParameters([])
                ->withRequestBody(new RequestBody(
                    'Проверка телефона',
                    new ArrayObject([
                        'application/json' => new MediaType(
                            new ArrayObject([
                                '$ref' => '#/components/schemas/Phone',
                            ])
                        ),
                    ])
                ))
                ->withResponses([
                    200 => new Response('', new ArrayObject([
                        'application/json' => new MediaType(
                            null,
                            ['message' => 'ok']
                        ),
                    ])),
                    429 => [],
                    409 => [],
                    400 => [],
                ])
        )->withDescription('Проверка phone'));

        return $this;
    }
}
