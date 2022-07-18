<?php

namespace App\UserBundle\Controller;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;

class CurrentUser
{
    public function __construct(
        private Security $security
    ) {}

    public function __invoke(Request $request): User
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
