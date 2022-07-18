<?php

namespace App\UserBundle\_Controller;

use App\Service\Code\Factories\MailService;
use App\Service\Code\SendSmsService;
use App\Entity\User;
use App\Service\Code\CodeService;
use App\UserBundle\Services\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ForgotPassword
{
    private $mailer;

    public function __construct(
        private EntityManagerInterface $em,
        private MailService $mailService,
        private SendSmsService $smsService,
        private ContainerInterface $container,
        private CodeService $coder,
        private ValidationService $validator)
    {
        $this->projectUrl = $container->getParameter('project_url');
    }

    public function __invoke(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent(), true);

        if (!isset($body['resetType'])) {
            return new JsonResponse(['message' => 'Укажите тип восстановления пароля'], 400);
        }

        if (User::ACTIVATION_TYPE_EMAIL === $body['resetType']) {
            if (!isset($body['email'])) {
                return new JsonResponse(['message' => 'Введите email!'], 400);
            }

            $email = $body['email'];
            $this->validator->validateEmail($email);

            /** @var User $user */
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (null === $user) {
                return new JsonResponse(['message' => 'Пользователь с таким email не зарегистрирован'], 400);
            }

            $codeObject = $this->coder->createActivationCode($user);

            $codeObject->user = $user;

            $this->em->persist($codeObject);
            $this->em->flush();

            $this->mailService->sendForgotPasswordMail($user->getEmail(), $codeObject);

            return new JsonResponse(['message' => 'Мы отправили на указанную вами почту письмо со ссылкой для изменения пароля.'], 201);
        }

        if (User::ACTIVATION_TYPE_PHONE === $body['resetType']) {
            if (!isset($body['phone'])) {
                return new JsonResponse(['message' => 'Введите телефон!'], 400);
            }

            $phone = $body['phone'];
            /** @var User $user */
            $user = $this->em->getRepository(User::class)->findOneBy(['phone' => $phone]);

            if (null === $user) {
                return new JsonResponse(['message' => 'Пользователь с таким номером телефона не зарегистрирован'], 400);
            }

            if (false === $user->isConfirmPhone) {
                return new JsonResponse(['message' => 'Номер телефона не подтвержден'], 400);
            }

            $codeObject = $this->coder->createActivationCode($user);

            $codeObject->user = $user;

            $this->em->persist($codeObject);
            $this->em->flush();

            $this->smsService->sendMessage(
                $body['phone'],
                "Код для смены пароля: $codeObject->code");

            return new JsonResponse(['message' => 'Мы отправили на указанный вами телефон sms со ссылкой для изменения пароля.'], 201);
        }

        return new JsonResponse(['message' => 'Указан неверный тип смены пароля'], 201);
    }
}
