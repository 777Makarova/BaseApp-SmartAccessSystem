<?php

namespace App\Security\User;

use App\Entity\User\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AgreementVoter extends Voter
{
    public const USE_SERVICE = 'use_service';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject):bool
    {
        if (!in_array($attribute, [self::USE_SERVICE])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token):bool
    {
        if (!$this->security->isGranted(User::ROLE_DEFAULT)) {
            return false;
        }

        $user = $token->getUser();

        /** @var User $authUser */
        $authUser = $this->security->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::USE_SERVICE:
                return $this->canUseService($authUser);
        }

        throw new LogicException('agreement voter internal error');
    }

    private function canUseService(User $user = null):bool
    {
        if (is_null($user)) {
            return false;
        }

        return $user->isAgreementAccepted;
    }
}
