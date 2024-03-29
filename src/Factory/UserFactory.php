<?php

namespace App\Factory;

use App\Entity\User\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<User>
 *
 * @method static User|Proxy createOne(array $attributes = [])
 * @method static User[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static User|Proxy find(object|array|mixed $criteria)
 * @method static User|Proxy findOrCreate(array $attributes)
 * @method static User|Proxy first(string $sortedField = 'id')
 * @method static User|Proxy last(string $sortedField = 'id')
 * @method static User|Proxy random(array $attributes = [])
 * @method static User|Proxy randomOrCreate(array $attributes = [])
 * @method static User[]|Proxy[] all()
 * @method static User[]|Proxy[] findBy(array $attributes)
 * @method static User[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method User|Proxy create(array|callable $attributes = [])
 */
final class UserFactory extends ModelFactory
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
        $fName = self::faker()->unique()->firstName();
        $lName = self::faker()->unique()->lastName();
        $mName = "Igorevich";
        $email = self::faker()->unique()->safeEmail();
        return [
            'enabled' => true,
            'username' => $username,
            'firstName' => $fName,
            'lastName' => $lName,
            'middleName' => $mName,
            'email' => $email,
            'password' => '12345678',
            'roles' => [],
            'isAgreementAccepted' => true,
            'isExternalUser' => false,
            'isRealEmail' => true,
            'isEmailConfirmed' => true,
            'isPhoneConfirmed' => true,
        ];
    }

    protected function initialize(): self
    {
        return $this
             ->afterInstantiate(function(User $user) {
                 $user->setEmailCanonical();
                 $user->setUsernameCanonical();
                 $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
             })
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
