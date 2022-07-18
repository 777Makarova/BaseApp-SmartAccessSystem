<?php

namespace App\OpenApi\File;

use ApiPlatform\Core\OpenApi\OpenApi;
use App\OpenApi\ExpandOpenApiInterface;

class ExpandFileOpenApi implements ExpandOpenApiInterface
{
    private OpenApi $openApi;

    public function apply(OpenApi $openApi): void
    {
        $this->openApi = $openApi;

        $this
            ->applyGetItem()
            ->applyGetCollection()
            ->applyCreate()
        ;
    }

    private function applyCreate(): void
    {
        $pathItem = $this->openApi->getPaths()->getPath('/files');
        $operation = $pathItem->getPost();

        $this->openApi->getPaths()->addPath('/files', $pathItem->withPost(
            $operation
                ->withSummary('Загрузка файла')
        ));
    }

    private function applyGetCollection(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/files');
        $operation = $pathItem->getGet();

        $this->openApi->getPaths()->addPath('/files', $pathItem->withGet(
            $operation
                ->withSummary('Получение списка файлов')
        ));

        return $this;
    }

    private function applyGetItem(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/files/{id}');
        $operation = $pathItem->getGet();

        $this->openApi->getPaths()->addPath('/files/{id}', $pathItem->withGet(
            $operation
                ->withSummary('Получение файла')
        ));

        return $this;
    }
}
