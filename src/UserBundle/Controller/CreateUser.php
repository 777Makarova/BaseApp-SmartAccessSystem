<?php

namespace App\UserBundle\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User\User;
use App\EventSubscriber\ActivateUserSubscriber;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateUser
{
    public function __construct(
        private EntityManagerInterface $em,
        private RateLimiterFactory $anonymousApiLimiter,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @param User $data
     * @param Request $request
     * @return User
     */
    public function __invoke(User $data, Request $request): User
    {
        $limiter = $this->anonymousApiLimiter->create($request->getClientIp());

        if (!$limiter->consume()->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }


        $data->setUsername(str_replace('+', '', $data->phone));

        $this->validator->validate($data);
        $data->setEmailCanonical();
        $data->setUsernameCanonical();


        // TODO Сделать метод приводящий логин к кононичному виду
        //$this->userManager->updateCanonicalFields($data);
        // TODO проверить работу
        $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));
        //$this->userManager->updatePassword($data);

        $this->em->persist($data);
        $this->em->flush();

        $request->attributes->set(ActivateUserSubscriber::REQUEST_ATTRIBUTE_NAME, $data);

        return $data;
    }
}
