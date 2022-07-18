<?php


namespace App\Service\User;


use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    public function __construct(private EntityManagerInterface $_em)
    {
    }

    private function findUserBy(array $criteria): UserInterface
    {
        $user = $this->_em->getRepository(User::class)->findOneBy($criteria);

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
