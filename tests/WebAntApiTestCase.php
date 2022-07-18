<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Story\DefaultFixturesStory;
use League\Bundle\OAuth2ServerBundle\Manager\Doctrine\AccessTokenManager;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

use League\Bundle\OAuth2ServerBundle\Entity\AccessToken as AccessTokenEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Client as ClientEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Scope as ScopeEntity;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken as AccessTokenModel;
use League\OAuth2\Server\CryptKey;

class WebAntApiTestCase extends ApiTestCase implements BaseTestInterface
{
    use ResetDatabase;
    use Factories;

    public const ENCRYPTION_KEY = 'supportWebant';
    public const PRIVATE_KEY_PATH = __DIR__ . '/Fixtures/private.key';
    public const PUBLIC_KEY_PATH = __DIR__ . '/Fixtures/public.key';

    public static function startUp(): void
    {
        self::bootKernel();

        DefaultFixturesStory::load();
    }

    public static function generateJwtToken(AccessTokenModel $accessToken): string
    {
        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($accessToken->getClient()->getIdentifier());
        $clientEntity->setRedirectUri(array_map('strval', $accessToken->getClient()->getRedirectUris()));

        $accessTokenEntity = new AccessTokenEntity();
        $accessTokenEntity->setPrivateKey(new CryptKey(self::PRIVATE_KEY_PATH, self::ENCRYPTION_KEY, false));
        $accessTokenEntity->setIdentifier(identifier: $accessToken->getIdentifier());
        $accessTokenEntity->setExpiryDateTime(dateTime: $accessToken->getExpiry());
        $accessTokenEntity->setClient($clientEntity);
        $accessTokenEntity->setUserIdentifier($accessToken->getUserIdentifier());

        foreach ($accessToken->getScopes() as $scope) {
            $scopeEntity = new ScopeEntity();
            $scopeEntity->setIdentifier((string) $scope);
            $accessTokenEntity->addScope($scopeEntity);
        }

        return (string) $accessTokenEntity;
    }

    public static function createClientWithCredentials(): Client
    {
        $accessManagers = static::getContainer()->get(AccessTokenManager::class);
        $accessToken = $accessManagers->find('admin');

        $token = self::generateJwtToken($accessToken);

        $defaultOptions = [
            'headers'=> array('Authorization'=>'Bearer '.$token),
        ];

        return static::createClient([], $defaultOptions);
    }

}
