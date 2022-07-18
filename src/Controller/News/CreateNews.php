<?php

namespace App\Controller\News;

use App\Entity\News\News;
use App\Entity\User\User;
use Symfony\Component\Security\Core\Security;

class CreateNews
{
    public function __construct(
        private Security $security
    ) {}

    public function __invoke(News $data): News
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $data->createdBy = $user;

        return $data;
    }
}
