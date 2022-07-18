<?php

namespace App\Factory;

use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\Model\Grant;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Client>
 *
 * @method static       Client|Proxy createOne(array $attributes = [])
 * @method static       Client[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static       Client|Proxy find(object|array|mixed $criteria)
 * @method static       Client|Proxy findOrCreate(array $attributes)
 * @method static       Client|Proxy first(string $sortedField = 'id')
 * @method static       Client|Proxy last(string $sortedField = 'id')
 * @method static       Client|Proxy random(array $attributes = [])
 * @method static       Client|Proxy randomOrCreate(array $attributes = [])
 * @method static       Client[]|Proxy[] all()
 * @method static       Client[]|Proxy[] findBy(array $attributes)
 * @method static       Client[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static       Client[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Client|Proxy create(array|callable $attributes = [])
 */
final class ClientFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {

        $name = self::faker()->word();
        return [
            'name' => $name,
            'secret' => '123',
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function(Client $client) {
               $client->setGrants(new Grant('refresh_token'), new Grant('password'));

            });
    }
    protected static function getClass(): string
    {
        return Client::class;
    }
}
