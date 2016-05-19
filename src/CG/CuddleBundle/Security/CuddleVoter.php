<?php

namespace CG\CuddleBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use CG\CuddleBundle\Entity\Cuddle;
use CG\UserBundle\Entity\User;

class CuddleVoter extends Voter
{
    const EDIT = 'edit';
    const VALIDATE = 'validate';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT, self::VALIDATE, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Cuddle) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }
        // if ($this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
        //     return true;
        // }

        $cuddle = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($cuddle, $user);
            case self::VALIDATE:
                return $this->canValidate($cuddle, $user);
            case self::DELETE:
                return $this->canDelete($cuddle, $user);
        }
    }

    private function canEdit(Cuddle $cuddle, User $user)
    {
        return $user !== $cuddle->getAuthor() && $user->hasRole('ROLE_MODERATOR');
    }

    private function canValidate(Cuddle $cuddle, User $user)
    {
        return $user !== $cuddle->getAuthor() && $user->hasRole('ROLE_MODERATOR');
    }

    private function canDelete(Cuddle $cuddle, User $user)
    {
        return $user !== $cuddle->getAuthor() && $user->hasRole('ROLE_MODERATOR');
    }
}