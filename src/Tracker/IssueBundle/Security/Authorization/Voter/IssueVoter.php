<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.02.15
 * Time: 18:58
 */

namespace Tracker\IssueBundle\Security\Authorization\Voter;

use Tracker\IssueBundle\Entity\Issue;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tracker\UserBundle\Entity\User;

class IssueVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';

    /**
     * @param string $attribute
     * @return bool
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::CREATE
        ), false);
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Tracker\IssueBundle\Entity\Issue';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param Issue $issue
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $issue, array $attributes)
    {
        if (!$this->supportsClass(get_class($issue))) {
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
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return self::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::VIEW:
                if ($this->userCanView($user, $issue)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::CREATE:
                if ($this->userCanCreate($user, $issue)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::EDIT:
                if ($this->userCanEdit($user, $issue)) {
                    return self::ACCESS_GRANTED;
                }
                break;
        }

        return  self::ACCESS_DENIED;
    }

    /**
     * @param User $user
     * @param Issue $issue
     * @return bool
     */
    public function userCanView($user, $issue)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            return true;
        }

        if ($this->operatorHasAccess($user, $issue)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Issue $issue
     * @return bool
     */
    public function userCanEdit($user, $issue)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            return true;
        }

        if ($this->operatorHasAccess($user, $issue)) {
            return true;
        }


        return false;
    }

    /**
     * @param User $user
     * @param Issue $issue
     * @return bool
     */
    public function userCanCreate($user, $issue)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            return true;
        }

        if ($this->operatorHasAccess($user, $issue)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Issue $issue
     * @return bool
     */
    protected function operatorHasAccess($user, $issue)
    {
        if ($issue->getProject()){
            $isMemberInProject = $issue->getProject()->getMembers()->contains($user);
        } else {
            $isMemberInProject = $user->getProduct()->count() > 0;
        }

        return $user->hasRole(User::ROLE_OPERATOR) && $isMemberInProject;
    }
}
