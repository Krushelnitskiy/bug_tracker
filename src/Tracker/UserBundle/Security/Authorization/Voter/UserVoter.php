<?php

namespace Tracker\UserBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Tracker\UserBundle\Entity\User;

class UserVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::CREATE
        ), false);
    }

    public function supportsClass($class)
    {

        $supportedClass = 'Tracker\UserBundle\Entity\User';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param null|User $user
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $user, array $attributes)
    {
        if (!$this->supportsClass(get_class($user))) {
            return self::ACCESS_ABSTAIN;
        }

        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException('Only one attribute is allowed for VIEW or EDIT or CREATE');
        }

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return self::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $currentUser = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::VIEW:
                if ($this->userCanView($currentUser, $user)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::CREATE:
                if ($this->userCanCreate($currentUser)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::EDIT:
                if ($this->userCanEdit($currentUser)) {
                    return self::ACCESS_GRANTED;
                }
                break;
        }

        return self::ACCESS_DENIED;
    }

    public function userCanView(User $currentUser, User $user)
    {
        if ($currentUser->hasRole(User::ROLE_ADMIN) ||
            $currentUser->getId() == $user->getId() ||
            $currentUser->hasRole(User::ROLE_MANAGER)) {
            return true;
        }

        return false;
    }

    public function userCanEdit(User $user)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        return false;
    }

    public function userCanCreate(User $user)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        return false;
    }
}
