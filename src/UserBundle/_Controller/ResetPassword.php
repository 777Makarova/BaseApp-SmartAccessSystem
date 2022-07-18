<?php

namespace App\UserBundle\_Controller;

use App\UserBundle\Entity\Code;
use App\UserBundle\Entity\User;
use App\UserBundle\Services\CodeService;
use App\UserBundle\Services\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebAnt\UserBundle\Model\UserManagerInterface;

class ResetPassword
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CodeService
     */
    private $codeService;

    /**
     * @var ValidationService
     */
    private $validator;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * ResetPassword constructor.
     */
    public function __construct(EntityManagerInterface $em, CodeService $codeService, ValidationService $validator, UserManagerInterface $userManager)
    {
        $this->em = $em;
        $this->codeService = $codeService;
        $this->validator = $validator;
        $this->userManager = $userManager;
    }

    public function __invoke(Request $request)
    {
        $body = json_decode($request->getContent());
        $code = $body->code;
        $newPassword = $body->newPassword;

        if (!isset($body->code)) {
            return new JsonResponse(['message' => 'Введите код подтверждения!'], 400);
        }

        if (!isset($body->newPassword)) {
            return new JsonResponse(['message' => 'Введите новый пароль!'], 400);
        }

        $this->validator->validatePassword($newPassword);

        /** @var Code $codeObject */
        $codeObject = $this->em->getRepository(Code::class)->findOneBy(['code' => $code]);

        if (!isset($codeObject)) {
            return new JsonResponse(['message' => 'Пользователя с данным кодом не существует.'], 400);
        }

        if (!$this->codeService->checkExpirationTime($codeObject)) {
            return new JsonResponse(['message' => 'Ваш код истёк.'], 400);
        }

        /** @var User $user */
        $user = $codeObject->user;

        if (null === $user) {
            return new JsonResponse(['message' => 'Пользователя не существует.'], 400);
        }

        $user->setPlainPassword($newPassword);

        $this->em->persist($user);
        $this->em->flush();

        $this->userManager->updatePassword($user);

        $this->userManager->updateUser($user);

        $this->codeService->deleteCode($codeObject);

        return new JsonResponse(['message' => 'Пароль изменён.'], 200);
    }
}
