<?php

namespace App\Tests;


use App\Factory\FileFactory;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileTest extends WebAntApiTestCase
{
    public function testUploadFile(): void
    {
        self::startUp();

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('default.storage');

        $file = new UploadedFile('./tests/Fixtures/files/photo.jpg', 'file.jpg');

        $client = static::createClientWithCredentials()->request('POST', '/files', [
            'headers' => [
                'accept' => 'application/json'
            ],
            'extra' => [
                'files' => [
                    $file
                ]
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($storage->fileExists(json_decode($client->getContent(), true)['path']));
        $this->assertResponseStatusCodeSame(201);

    }

    public function testUploadFileNotAuth(): void
    {
        self::startUp();

        $file = new UploadedFile('./tests/Fixtures/files/photo.jpg', 'file.jpg');

        static::createClient()->request('POST', '/files', [
            'headers' => [
                'accept' => 'application/json'
            ],
            'extra' => [
                'files' => [
                    $file
                ]
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetFiles(): void
    {
        self::startUp();

        FileFactory::createMany(15);

        $response = static::createClientWithCredentials()->request('GET', "/files");

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/File',
            '@id' => '/files',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 15,
        ]);

        $this->assertCount(15, $response->toArray()['hydra:member']);
    }

    public function testFileFormat()
    {
        self::startUp();

        $file = new UploadedFile('./tests/Fixtures/files/file.ico', 'file.ico');

        static::createClientWithCredentials()->request('POST', '/files', [
            'headers' => [
                'accept' => 'application/json'
            ],
            'extra' => [
                'files' => [
                    $file
                ]
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);
    }
}
