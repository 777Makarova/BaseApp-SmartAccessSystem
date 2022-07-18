<?php

namespace App\UserBundle\OpenApi\User;

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
            ->applyCurrent()
            ->applyCreate()
            ->applyGetItem()
            ->applyGetCollection()
            ->applyDeleteItem()
            ->applyUpdateItem()
        ;
    }

    private function applyCurrent(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/users/current');
        $operation = $pathItem->GetGet();

        $this->openApi->getPaths()->addPath('/users/current', $pathItem->withGet(
            $operation
                ->withSummary('Текущий авторизованный пользователь')
                ->withParameters([])
                ->withResponses([
                    200 => new Response('', new ArrayObject([
                        'application/json' => new MediaType(
                            new ArrayObject([
                                '$ref' => '#/components/schemas/User-GetUser_GetObjUser',
                            ])
                        ),
                    ])),
                    429 => [],
                    403 => [],
                ])
        )->withSummary('Получить текущего авторизованного пользователя'));

        return $this;
    }

    private function applyCreate(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/users');
        $operation = $pathItem->getPost();

        $this->openApi->getPaths()->addPath('/users', $pathItem->withPost(
            $operation
                ->withSummary('Создание/Регистрация пользователя')
        ));

        return $this;
    }

    private function applyGetCollection(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/users');
        $operation = $pathItem->getGet();

        $this->openApi->getPaths()->addPath('/users', $pathItem->withGet(
            $operation
                ->withSummary('Получение списка пользователей')
        ));

        return $this;
    }

    private function applyGetItem(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/users/{id}');
        $operation = $pathItem->getGet();

        $this->openApi->getPaths()->addPath('/users/{id}', $pathItem->withGet(
            $operation
                ->withSummary('Получение пользователя')
        ));

        return $this;
    }

    private function applyDeleteItem(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/users/{id}');
        $operation = $pathItem->getDelete();

        $this->openApi->getPaths()->addPath('/users/{id}', $pathItem->withDelete(
            $operation
                ->withSummary('Удаление пользователя')
        ));

        return $this;
    }

    private function applyUpdateItem(): void
    {
        $pathItem = $this->openApi->getPaths()->getPath('/users/{id}');
        $operation = $pathItem->getPut();

        $this->openApi->getPaths()->addPath('/users/{id}', $pathItem->withPut(
            $operation
                ->withSummary('Обновление пользователя')
        ));

    }
}
