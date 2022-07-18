<?php

namespace App\Controller\Admin;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Admin\Admin;

use App\EventSubscriber\ActivateUserSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class CreateAdmin
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {}

        public function __invoke(Admin $data, Request $request): Admin
        {

            $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPassword()));


            return $data;
        }


//    /**
//     * @param Admin $data
//     * @param Request $request
//     * @return Admin
//     */
//    public function __invoke(Admin $data, Request $request): Admin
//    {
//        $limiter = $this->anonymousApiLimiter->create($request->getClientIp());
//
//        if (!$limiter->consume()->isAccepted()) {
//            throw new TooManyRequestsHttpException();
//        }
//
//
//
//        $this->validator->validate($data);
//
//
//        // TODO Сделать метод приводящий логин к кононичному виду
//        //$this->userManager->updateCanonicalFields($data);
//        // TODO проверить работу
//        $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPassword()));
//        //$this->userManager->updatePassword($data);
//
//        $this->em->persist($data);
//        $this->em->flush();
//
//        $request->attributes->set(ActivateUserSubscriber::REQUEST_ATTRIBUTE_NAME, $data);
//
//        return $data;
//    }




}