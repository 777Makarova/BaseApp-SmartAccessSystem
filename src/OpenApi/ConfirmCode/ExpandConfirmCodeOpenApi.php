<?php

namespace App\OpenApi\ConfirmCode;

use ApiPlatform\Core\OpenApi\OpenApi;
use App\OpenApi\ExpandOpenApiInterface;

class ExpandConfirmCodeOpenApi implements ExpandOpenApiInterface
{
    private OpenApi $openApi;

    public function apply(OpenApi $openApi): void
    {
        $this->openApi = $openApi;

        $this
            ->applyResendActivationCode()
            ->applyPasswordReset()
            ->applyConfirmCode()
        ;
    }

    private function applyConfirmCode(): void
    {
        $pathItem = $this->openApi->getPaths()->getPath('/confirm-code');
        $operation = $pathItem->getPost();

        $this->openApi->getPaths()->addPath('/confirm-code', $pathItem->withPost(
            $operation
                ->withSummary('Подтверждение пользователя')
                ->withDescription('Использование кода подтверждения. В зависимости от типа кода это может 
                быть подтверждение пользователя, подтверждение почты/телефона или смена пароля.')
        ));
    }

    private function applyPasswordReset(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/password/reset');
        $operation = $pathItem->getPost();

        $this->openApi->getPaths()->addPath('/password/reset', $pathItem->withPost(
            $operation
                ->withSummary('Запросить смену пароля')
        ));

        return $this;
    }

    private function applyResendActivationCode(): self
    {
        $pathItem = $this->openApi->getPaths()->getPath('/resend-activation-code');
        $operation = $pathItem->getPost();

        $this->openApi->getPaths()->addPath('/resend-activation-code', $pathItem->withPost(
            $operation
                ->withSummary('Запросить новый код для подтверждения аккаунта')
        ));

        return $this;
    }
}
