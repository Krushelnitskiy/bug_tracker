<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.02.15
 * Time: 18:58
 */

namespace Tracker\ProjectBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Tracker\ProjectBundle\Entity\Project;
use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Issue;

class ProjectVoter implements VoterInterface
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
        $supportedClass = 'Tracker\ProjectBundle\Entity\Project';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param null|Issue $issue
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

        return self::ACCESS_DENIED;
    }

    public function userCanView(User $user, Project $project)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            return true;
        }

        if ($project->getMembers()->count() > 0) {
            $isMemberProject = $project->getMembers()->contains($user);
        } else {
            $isMemberProject = $user->getProduct()->count() > 0;
        }

        $operatorCanView = $user->hasRole(User::ROLE_OPERATOR) && $isMemberProject;

        if ($operatorCanView) {
            return true;
        }

        return false;
    }

    public function userCanEdit(User $user)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            return true;
        }
        return false;
    }

    public function userCanCreate(User $user)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            return true;
        }

        return false;
    }
}
