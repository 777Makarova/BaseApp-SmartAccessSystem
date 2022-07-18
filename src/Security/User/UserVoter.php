<?php

namespace App\Security\User;

use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    public const CAN_EDIT = 'can_edit';
    public const CAN_VIEW = 'can_view';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject):bool
    {
        if (!in_array($attribute, [
            self::CAN_EDIT,
            self::CAN_VIEW,
        ])) {
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

        if (!$subject instanceof User) {
            return false;
        }

        $user = $subject;

        switch ($attribute) {
            case self::CAN_EDIT:
            case self::CAN_VIEW:
                return $this->canView($user);
        }

        return false;
    }

    protected function canView(User $user):bool
    {
        /** @var User $authUser */
        $authUser = $this->security->getUser();

        if ($authUser->hasRole(User::ROLE_SUPER_ADMIN)) {
            return true;
        }
        if ($user->getId() === $authUser->getId()) {
            return true;
        }

        return false;
    }
}
