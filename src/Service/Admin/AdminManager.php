<?php

namespace App\Service\Admin;


use App\Entity\Admin\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminManager
{
    public function __construct(private EntityManagerInterface $_em)
    {
    }

    private function findUserBy(array $criteria): UserInterface
    {
        $user = $this->_em->getRepository(Admin::class)->findOneBy($criteria);

        if(!$user instanceof UserInterface){
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findUserByIdentifier(string $identifier): UserInterface
    {
        return $this->findUserBy(['username'=>$identifier]);
    }
}
