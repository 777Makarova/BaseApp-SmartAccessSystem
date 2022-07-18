<?php

namespace App\Factory;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class JWTConfigurationFactory
{
    public function __invoke(string $privateKey, string $publicKey, string $keyPhrase): Configuration
    {
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($privateKey, $keyPhrase),
            InMemory::plainText('')
        );
    }
}