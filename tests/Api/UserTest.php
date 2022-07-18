<?php


namespace App\Tests;


use App\Entity\User\User;
use App\Service\User\UserManager;
use App\UserBundle\Repository\UserRepository;
use League\Bundle\OAuth2ServerBundle\Manager\Doctrine\AccessTokenManager;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken;
use League\Bundle\OAuth2ServerBundle\Security\Authentication\Token\OAuth2Token;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserTest extends WebAntApiTestCase
{

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testGetUsers(): void
    {
        self::startUp();

        $response = static::createClientWithCredentials()->request(
            'GET',
            '/users');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 12,
        ]);

        $this->assertCount(12, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetUserNotAuth():void
    {
        self::startUp();

        static::createClient()->request('GET', '/users');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testRegistrationUser():void
    {
        self::startUp();
        static::createClient()->request('POST', '/users', ['json' => [
            'isAgreementAccepted' => true,
            'firstName' => 'Vladimir',
            'lastName' => 'Daron',
            'middleName' => 'Igorevich',
            'phone' => '+79000000001',
            'plainPassword' => '12345678',
            'email' => 'e@webant.ru',
        ]]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCurrent():void
    {
        self::startUp();

        $response = static::createClientWithCredentials()->request(
            'GET',
            '/users/current');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => '70000000000'
        ]);

    }

    public function testCurrentNotAuth():void
    {
        self::startUp();

        static::createClient()->request(
            'GET',
            '/users/current');

        $this->assertResponseStatusCodeSame(401);
    }

}
