<?php

namespace App\OpenApi\News;

use ApiPlatform\Core\OpenApi\OpenApi;
use App\OpenApi\ExpandOpenApiInterface;

class ExpandNewsOpenApi implements ExpandOpenApiInterface
{
    private OpenApi $openApi;

    public function apply(OpenApi $openApi): void
    {
        $this->openApi = $openApi;

        $this
            ->applyGetItem()
            ->applyGetCollection()
            ->applyUpdateItem()
            ->applyDeleteItem()
            ->applyCreate()
        ;
    }

    private function applyCreate(): void
    {
        $pathItem = $this->openApi->getPaths()->getPath('/news');
        $operation = $pathItem->getPost();

        $this->openApi->getPaths()->addPath('/news', $pathItem->withPost(
            $operation
                ->withSummary('Создание новости')
        ));
    }

    private function applyGetCollection(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/news');
        $operation = $pathItem->getGet();

        $this->openApi->getPaths()->addPath('/news', $pathItem->withGet(
            $operation
                ->withSummary('Получение списка новостей')
        ));

        return $this;
    }

    private function applyGetItem(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/news/{id}');
        $operation = $pathItem->getGet();

        $this->openApi->getPaths()->addPath('/news/{id}', $pathItem->withGet(
            $operation
                ->withSummary('Получение новости')
        ));

        return $this;
    }

    private function applyDeleteItem(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/news/{id}');
        $operation = $pathItem->getDelete();

        $this->openApi->getPaths()->addPath('/news/{id}', $pathItem->withDelete(
            $operation
                ->withSummary('Удаление новости')
        ));

        return $this;
    }

    private function applyUpdateItem(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/news/{id}');
        $operation = $pathItem->getPut();

        $this->openApi->getPaths()->addPath('/news/{id}', $pathItem->withPut(
            $operation
                ->withSummary('Обновление новости')
        ));

        return $this;
    }
}
