<?php

namespace Tracker\ProjectBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Tracker\ProjectBundle\Entity\Project;
use Tracker\UserBundle\Entity\User;

class ProjectVoter implements VoterInterface
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
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Tracker\ProjectBundle\Entity\Project';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $project, array $attributes)
    {
        if (!$this->supportsClass(get_class($project))) {
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
        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::VIEW:
                if ($this->userCanView($user, $project)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::CREATE:
                if ($this->userCanCreate($user, $project)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::EDIT:
                if ($this->userCanEdit($user, $project)) {
                    return self::ACCESS_GRANTED;
                }
                break;
        }

        return self::ACCESS_DENIED;
    }

    /**
     * @param User $user
     * @param Project $project
     *
     * @return bool
     */
    protected function userCanView(User $user, Project $project)
    {
        if ($this->isSuperUser($user)) {
            return true;
        }

        if ($project->getMembers()->count() > 0) {
            $isMemberProject = $project->getMembers()->contains($user);
        } else {
            $isMemberProject = $user->getProject()->count() > 0;
        }

        $operatorCanView = $user->hasRole(User::ROLE_OPERATOR) && $isMemberProject;

        if ($operatorCanView) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    protected function userCanEdit(User $user)
    {
        if ($this->isSuperUser($user)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    protected function userCanCreate(User $user)
    {
        if ($this->isSuperUser($user)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    protected function isSuperUser(User $user)
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
