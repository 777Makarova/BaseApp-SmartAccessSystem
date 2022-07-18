<?php

namespace App\Factory;

use App\Entity\File\File;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<File>
 *
 * @method static     File|Proxy createOne(array $attributes = [])
 * @method static     File[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static     File|Proxy find(object|array|mixed $criteria)
 * @method static     File|Proxy findOrCreate(array $attributes)
 * @method static     File|Proxy first(string $sortedField = 'id')
 * @method static     File|Proxy last(string $sortedField = 'id')
 * @method static     File|Proxy random(array $attributes = [])
 * @method static     File|Proxy randomOrCreate(array $attributes = [])
 * @method static     File[]|Proxy[] all()
 * @method static     File[]|Proxy[] findBy(array $attributes)
 * @method static     File[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static     File[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method File|Proxy create(array|callable $attributes = [])
 */
final class FileFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'path' => self::faker()->text(14),
        ];
    }

    protected static function getClass(): string
    {
        return File::class;
    }
}
