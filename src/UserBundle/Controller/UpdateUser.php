<?php

namespace App\UserBundle\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\OAuth\AccessToken;
use App\Entity\User\User;
use App\Service\User\ConfirmationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use App\Model\UserManagerInterface;

class UpdateUser extends AbstractController
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private ConfirmationService $confirmationService,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(User $data, User $previous_data)
    {
        $this->validator->validate($data);

        $token = $this->security->getToken()->getCredentials();

        $this->confirmationService->updateUserConfirmationCheck($data, $previous_data);

        /** @var AccessToken $accessTooken */
        $accessToken = $this->em->getRepository(AccessToken::class)
            ->findOneBy(['token' => $token]);

        if (str_contains($accessToken->getScope(), 'changePassword')) {

            // TODO проверить работу
            $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));
            //$this->userManager->updatePassword($data);
        }

        // TODO Сделать метод приводящий логин к кононичному виду
        //$this->userManager->updateCanonicalFields($data);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
