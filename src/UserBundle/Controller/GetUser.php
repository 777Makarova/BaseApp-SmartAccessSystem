<?php

namespace App\UserBundle\Controller;

use App\Entity\User\User;
use Symfony\Component\Security\Core\Security;

class GetUser
{
    public function __construct(
        private Security $security
    ) {}

    public function __invoke(User $data)
    {
        return $data;
    }
}
