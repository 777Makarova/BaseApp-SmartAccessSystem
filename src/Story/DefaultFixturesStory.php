<?php

namespace App\Story;

use App\Factory\AccessTokenFactory;
use App\Factory\ClientFactory;
use App\Factory\NewsFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class DefaultFixturesStory extends Story
{
    public function build(): void
    {
        $userAdmin = UserFactory::createOne([
            'username' => '70000000000',
            'roles' => ['ROLE_ADMIN','ROLE_USER'],
        ]);

        $user = UserFactory::createOne([
            'username' => '70000000001',
            'roles' => ['ROLE_EDITOR'],
        ]);

        $client = ClientFactory::createOne([
            'identifier' => '123',
        ]);

        AccessTokenFactory::createOne([
            'identifier' => 'admin',
            'client' => $client,
            'expiry' => new \DateTimeImmutable('+1 hour'),
            'user_identifier' => $userAdmin->getUsername(),
            'scopes' => [],
        ]);

        AccessTokenFactory::createOne([
            'identifier' => 'editor',
            'client' => $client,
            'expiry' => new \DateTimeImmutable('+1 hour'),
            'user_identifier' => $user->getUsername(),
            'scopes' => [],
        ]);

        UserFactory::createMany(10);

    }
}
