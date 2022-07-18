<?php

namespace App\Factory;

use JetBrains\PhpStorm\ArrayShape;
use \League\Bundle\OAuth2ServerBundle\Model\AccessToken;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<AccessToken>
 *
 * @method static            AccessToken|Proxy createOne(array $attributes = [])
 * @method static            AccessToken[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static            AccessToken|Proxy find(object|array|mixed $criteria)
 * @method static            AccessToken|Proxy findOrCreate(array $attributes)
 * @method static            AccessToken|Proxy first(string $sortedField = 'id')
 * @method static            AccessToken|Proxy last(string $sortedField = 'id')
 * @method static            AccessToken|Proxy random(array $attributes = [])
 * @method static            AccessToken|Proxy randomOrCreate(array $attributes = [])
 * @method static            AccessToken[]|Proxy[] all()
 * @method static            AccessToken[]|Proxy[] findBy(array $attributes)
 * @method static            AccessToken[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static            AccessToken[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method AccessToken|Proxy create(array|callable $attributes = [])
 */
final class AccessTokenFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
        ];
    }

    protected static function getClass(): string
    {
        return AccessToken::class;
    }
}
