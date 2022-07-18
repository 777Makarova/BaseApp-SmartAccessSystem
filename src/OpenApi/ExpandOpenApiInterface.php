<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\OpenApi;

interface ExpandOpenApiInterface
{
    public function apply(OpenApi $openApi): void;
}
