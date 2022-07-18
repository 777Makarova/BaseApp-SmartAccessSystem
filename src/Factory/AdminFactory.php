<?php

namespace App\Factory;




use App\Entity\Admin\Admin;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Admin>
 *
 * @method static Admin|Proxy createOne(array $attributes = [])
 * @method static Admin[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Admin|Proxy find(object|array|mixed $criteria)
 * @method static Admin|Proxy findOrCreate(array $attributes)
 * @method static Admin|Proxy first(string $sortedField = 'id')
 * @method static Admin|Proxy last(string $sortedField = 'id')
 * @method static Admin|Proxy random(array $attributes = [])
 * @method static Admin|Proxy randomOrCreate(array $attributes = [])
 * @method static Admin[]|Proxy[] all()
 * @method static Admin[]|Proxy[] findBy(array $attributes)
 * @method static Admin[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Admin[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Admin|Proxy create(array|callable $attributes = [])
 */
final class AdminFactory extends ModelFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder
    )
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $username = self::faker()->unique()->userName();

        return [
            'enabled' => true,
            'username' => $username,
            'password' => '12345678',
            'roles' => [],
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function(Admin $admin) {
                $admin->setPassword($this->passwordEncoder->hashPassword($admin, $admin->getPassword()));
            })
            ;
    }

    protected static function getClass(): string
    {
        return Admin::class;
    }
}

