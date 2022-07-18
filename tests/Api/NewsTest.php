<?php

namespace App\Tests;

use App\Entity\News\News;
use App\Factory\NewsFactory;
use App\Factory\UserFactory;
use League\Bundle\OAuth2ServerBundle\Manager\Doctrine\AccessTokenManager;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class NewsTest extends WebAntApiTestCase
{
    /**
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testGetCollection(): void
    {

        self::startUp();
        NewsFactory::createMany(101, function () {
            return [
                'createdBy' => UserFactory::random(),
            ];
        });

        $response = static::createClient()->request('GET', '/news');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/News',
            '@id' => '/news',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 101,
            'hydra:view' => [
                '@id' => '/news?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/news?page=1',
                'hydra:last' => '/news?page=4',
                'hydra:next' => '/news?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(News::class);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCreateNewsNotAuth(): void
    {
        self::startUp();

        static::createClient()->request('POST', '/news', ['json' => [
            'title' => 'The Handmaid\'s Tale',
            'text' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
            'images' => ['image1'],
            'date' => '2021-08-04T21:20:43.634Z',
        ]]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateNews(): void
    {
        self::startUp();
        static::createClientWithCredentials()->request('POST', '/news', ['json' => [
            'title' => 'The Handmaid\'s Tale',
            'text' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
            'date' => '2021-08-04T21:20:43.634Z',
        ]]);

        $this->assertResponseStatusCodeSame(201);
    }
}
