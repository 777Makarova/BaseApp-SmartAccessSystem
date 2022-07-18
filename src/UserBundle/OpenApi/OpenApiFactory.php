<?php

namespace App\UserBundle\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\OpenApi\ConfirmCode\ExpandConfirmCodeOpenApi;
use App\OpenApi\Oauth\ExpandOAuthOpenApi;
use App\OpenApi\User\ExpandUserOpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    private const EXPANDS = [
        ExpandUserOpenApi::class,
    ];

    public function __construct(
        private OpenApiFactoryInterface $decorated
    ){}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $this->applyExpands($openApi);

        return $openApi;
    }

    private function applyExpands(OpenApi $openApi) {
        /** @var ExpandOpenApiInterface $expand */
        foreach (self::EXPANDS as $expand) {
            (new($expand))->apply($openApi);
        }
    }

}
