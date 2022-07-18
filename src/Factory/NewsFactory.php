<?php

namespace App\Factory;

use App\Entity\News\News;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<News>
 *
 * @method static     News|Proxy createOne(array $attributes = [])
 * @method static     News[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static     News|Proxy find(object|array|mixed $criteria)
 * @method static     News|Proxy findOrCreate(array $attributes)
 * @method static     News|Proxy first(string $sortedField = 'id')
 * @method static     News|Proxy last(string $sortedField = 'id')
 * @method static     News|Proxy random(array $attributes = [])
 * @method static     News|Proxy randomOrCreate(array $attributes = [])
 * @method static     News[]|Proxy[] all()
 * @method static     News[]|Proxy[] findBy(array $attributes)
 * @method static     News[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static     News[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method News|Proxy create(array|callable $attributes = [])
 */
final class NewsFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->text(10),
            'text' => self::faker()->text(),
            'date' => self::faker()->dateTime(),
        ];
    }

    protected static function getClass(): string
    {
        return News::class;
    }
}
