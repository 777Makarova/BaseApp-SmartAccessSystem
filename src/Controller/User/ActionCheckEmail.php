<?php

namespace App\Controller\User;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionCheckEmail
{
    public function __construct(
        private RateLimiterFactory $personalSensitiveLimiter,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private TranslatorInterface $translator
    ) {}

    public function __invoke(Request $request)
    {
        $limiter = $this->personalSensitiveLimiter->create($request->getClientIp());

        if (!$limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $data = json_decode($request->getContent());

        if (!isset($data->email)) {
            throw new BadRequestHttpException();
        }

        $q = $this->em->createQueryBuilder();
        $isExistUser = $q->select('1')
            ->from(User::class, 'user')
            ->where('user.email = :email')
            ->setParameter('email', $data->email)
            ->getQuery()->getOneOrNullResult();

        if ($isExistUser) {
            throw new ConflictHttpException($this->translator->trans('user.registration.errors.user_exists'));
        }

        return new JsonResponse([
            'message' => 'ok',
        ]);
    }
}
