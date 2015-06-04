<?php

namespace Tracker\IssueBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Tracker\IssueBundle\Entity\Issue;
use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Type;

class IssueVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const CREATE_SUB_TASK = 'create_sub_task';

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::CREATE,
            self::CREATE_SUB_TASK
        ), false);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Tracker\IssueBundle\Entity\Issue';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * {@inheritdoc}
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


        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::VIEW:
            case self::CREATE:
            case self::EDIT:
                if ($this->userHasAccess($user, $issue)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::CREATE_SUB_TASK:
                if ($this->userHasAccess($user, $issue) && $issue->getType()->getValue() === Type::TYPE_STORY) {
                    return self::ACCESS_GRANTED;
                }
                break;
        }

        return  self::ACCESS_DENIED;
    }

    /**
     * @param $user
     * @param $issue
     *
     * @return bool
     */
    protected function userHasAccess($user, $issue)
    {
        if ($this->isSuperUser($user)) {
            return true;
        }

        if ($this->operatorHasAccess($user, $issue)) {
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

    /**
     * @param User $user
     * @param Issue $issue
     *
     * @return bool
     */
    protected function operatorHasAccess($user, $issue)
    {
        if ($issue->getProject()) {
            $isMemberInProject = $issue->getProject()->getMembers()->contains($user);
        } else {
            $isMemberInProject = $user->getProject()->count() > 0;
        }

        return $user->hasRole(User::ROLE_OPERATOR) && $isMemberInProject;
    }
}
