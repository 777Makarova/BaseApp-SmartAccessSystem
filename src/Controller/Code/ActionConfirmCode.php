<?php

namespace App\Controller\Code;

use App\Entity\Code\Code;
use App\Entity\Code\ConfirmCodeRequest;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionConfirmCode extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $trans,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @param ConfirmCodeRequest $data
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function __invoke(ConfirmCodeRequest $data): JsonResponse
    {
        /** @var Code $code */
        $q = $this->em->createQueryBuilder();
        $code = $q->select('code')
            ->from(Code::class, 'code')
            ->leftJoin('code.user', 'user')
            ->where('code.code = :code')
            ->andWhere('user.phone  = :phone')
            ->setParameters([
                'code' => $data->code,
                'phone' => $data->phone,
            ])
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);

        if (is_null($code)) {
            throw new NotFoundHttpException($this->trans->trans('user.authorization.errors.code_not_found'));
        }

        if ($code->isExpired()) {
            throw new NotFoundHttpException($this->trans->trans('user.authorization.errors.code_is_expired'));
        }
        $user = $code->user;

        $messageData = [
            'username' => $user->getUsername(),
        ];

        switch ($code->codeType) {
            case Code::TYPE_ACTIVATE:
                $user->enabled = true;
                $messageText = $this->trans->trans('user.authorization.confirmation.messages.activated');
                break;
            case Code::TYPE_CONFIRM_EMAIL:
                $user->email = $code->email;
                $user->emailCanonical = $code->email;
                $messageText = $this->trans->trans('user.authorization.confirmation.messages.email_confirmed');
                $messageData['newEmail'] = $code->email;
                $user->isEmailConfirmed = true;
                $user->isRealEmail = true;
                break;
            case Code::TYPE_CONFIRM_PHONE:
                $user->phone = $code->phone;
                $messageText = $this->trans->trans('user.authorization.confirmation.messages.phone_confirmed');
                $messageData['newPhone'] = $code->phone;
                $user->isPhoneConfirmed = true;
                $user->setUsername($code->phone);
                break;
            case Code::TYPE_RESET_PASSWORD:
                if (!$data->newPassword) {
                    $status = Response::HTTP_BAD_REQUEST;
                    $messageText = $this->trans->trans('user.authorization.confirmation.errors.no_new_password');
                    break;
                }
                $user->setPassword($this->passwordHasher->hashPassword($user, $data->newPassword));
                $messageText = $this->trans->trans('user.authorization.confirmation.messages.password_changed');
                break;
            default:
                $messageText = $this->trans->trans('user.authorization.confirmation.errors.unknown_code_type');
                $status = Response::HTTP_BAD_REQUEST;
        }

        $this->em->remove($code);
        $this->em->persist($user);
        $this->em->flush();

        if (
            in_array($code->codeType, [Code::TYPE_ACTIVATE, Code::TYPE_RESET_PASSWORD])
            && Code::SENT_BY_SMS == $code->sentBy
        ) {
            /** @var AccessTokenRepositoryInterface $accessTokenRepository */
            $accessTokenRepository = $this->em->getRepository(AccessToken::class);
            $accessTokenRepository->
            $clientRepository = $this->em->getRepository(Client::class);
//            $accessToken = $accessTokenRepository->getNewToken($clientRepository->findOneBy(['grants' => 'reset_password']), ['reset_password'], $data->phone);
//            $this->em->persist($accessToken);
//            $this->em->flush();

            return new JsonResponse([]);
        }

        $messageData['message'] = $messageText;

        return new JsonResponse($messageData, $status ?? Response::HTTP_OK);
    }
}
