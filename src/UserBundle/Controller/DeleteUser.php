<?php
/**
 * Created by PhpStorm.
 * User: zawert
 * Date: 14.05.18
 * Time: 18:34.
 */

namespace App\UserBundle\Controller;

use App\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Security;

class DeleteUser
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(EntityManagerInterface $em, User $data): JsonResponse
    {
        if (!$this->security->isGranted('ROLE_ADMIN') && !$this->security->isGranted('ROLE_MANAGER')) {
            if ($this->security->getToken()->getUser()->getId() != $data->getId()) {
                throw new HttpException(403, 'Forbidden');
            }
        }
        $data->setEnabled(false);
        $em->persist($data);
        $em->flush();

        return new JsonResponse(['ms' => 'ok'], 204);
    }
}
