<?php

namespace App\Controller\Code;

use App\Entity\Code\Code;
use App\Entity\Code\ConfirmCodeRequest;
use App\Entity\User\User;
use App\Service\Code\CodeService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionResetPassword
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private CodeService $codeService,
        private TranslatorInterface $translator
    ) {}

    /**
     * @return JsonResponse
     */
    public function __invoke(ConfirmCodeRequest $data)
    {
        if (empty($data->phone)) {
            throw new BadRequestHttpException($this->translator->trans('user.authorization.confirmation.errors.no_phone'));
        }

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy([
            'username' => $data->phone,
            'enabled' => true,
        ]);

        if (!$user) {
            throw new NotFoundHttpException($this->translator->trans('user.authorization.errors.user_not_found'));
        }

        if ($user->lastConfirmCodeDate && new DateTime('-1 minute') <= $user->lastConfirmCodeDate) {
            throw new ConflictHttpException($this->translator->trans('user.authorization.confirmation.errors.code_limit'));
        }

        $code = $this->codeService->createCodeWithPhone($user, $user->phone, Code::TYPE_RESET_PASSWORD);

        $code->codeType = Code::TYPE_RESET_PASSWORD;
        $code->sentBy = Code::SENT_BY_SMS;

        $user->lastConfirmCodeDate = new DateTime();

        $this->em->persist($code);
        $this->em->persist($user);
        $this->em->flush();

        $this->codeService->sendCode($code);

        return new JsonResponse([
            'message' => 'ok',
        ]);
    }
}
