<?php

namespace App\Tests;


use League\Bundle\OAuth2ServerBundle\Model\Client;
use App\Factory\ClientFactory;

class OauthTest extends WebAntApiTestCase
{
    public function testGetAccessToken(): void
    {
        self::startUp();

        /** @var Client $client */
        $client = ClientFactory::find(['identifier' => '123']);

        $options['extra']['parameters'] = [
            'client_id' => $client->getIdentifier(),
            'client_secret' => $client->getSecret(),
            'grant_type' => 'password',
            'username' => '70000000000',
            'password' => '12345678',
        ];

        static::createClient()->request('POST', '/token', $options);

        $this->assertResponseIsSuccessful();
    }

    public function testFailLogin(): void
    {
        self::startUp();

        $options['extra']['parameters'] = [
            'client_id' => '123',
            'client_secret' => '123',
            'grant_type' => 'password',
            'username' => '70000000001',
            'password' => 'pass',
        ];

        static::createClient()->request('POST', '/token', $options);

        $this->assertResponseStatusCodeSame(400);
    }
}
