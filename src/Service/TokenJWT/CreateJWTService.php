<?php

namespace App\Service\TokenJWT;

use Lcobucci\JWT\Configuration;

class CreateJWTService
{
    public function __construct(private Configuration $configuration)
    {
    }

    public function generateJWT ($user_id, $roleSet): string
    {
        $now = new \DateTimeImmutable();
        $token = $this->configuration
            ->builder()
            ->issuedAt($now)
            ->withClaim('user_id', $user_id)
            ->withClaim('scope',$roleSet)
            ->getToken($this->configuration->signer(),$this->configuration->signingKey());

        return $token->toString();

    }

}