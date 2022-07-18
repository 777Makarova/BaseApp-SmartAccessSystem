<?php

namespace App\UserBundle\_Controller;

use App\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use WebAnt\UserBundle\Model\UserManagerInterface;

class UpdatePassword
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(UserPasswordEncoderInterface $encoder, Request $request, UserManagerInterface $userManager): JsonResponse
    {
        $body = json_decode($request->getContent());

        $user = $this->security->getUser();

        if (!$user) {
            throw new UnauthorizedHttpException('', 'Unauthorized');
        }

        if (isset($body->newPassword) && empty($body->newPassword)) {
            return new JsonResponse(['ms' => 'Укажите новый пароль'], 400);
        }

        if (isset($body->oldPassword) && empty($body->oldPassword)) {
            return new JsonResponse(['ms' => 'Укажите старый пароль'], 400);
        }

        $passwordValid = $encoder->isPasswordValid($user, $body->oldPassword);
        if (!$passwordValid) {
            return new JsonResponse(['ms' => 'Укажите старый пароль'], 400);
        }

        /** @var User $userOdj */
        $userOdj = $this->em->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        $userOdj->setPlainPassword($body->newPassword);
        $this->em->persist($userOdj);
        $this->em->flush();

        $userManager->updatePassword($user);

        $userManager->updateUser($user);

        return new JsonResponse(['message' => 'Пароль обновлен'], 200);
    }
}
