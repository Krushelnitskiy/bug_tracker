<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.02.15
 * Time: 18:58
 */

namespace Tracker\IssueBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Tracker\IssueBundle\Entity\Comment;
use Tracker\UserBundle\Entity\User;

class CommentVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::CREATE,
            self::DELETE
        ), false);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Tracker\IssueBundle\Entity\Comment';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $comment, array $attributes)
    {

        if (!$this->supportsClass(get_class($comment))) {
            return self::ACCESS_ABSTAIN;
        }

        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException('Only one attribute is allowed for VIEW or EDIT or CREATE');
        }

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return self::ACCESS_ABSTAIN;
        }

        /**
         * @var user User
         */
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::EDIT:
                if ($this->userHasAccess($user, $comment)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::DELETE:
                if ($this->userHasAccess($user, $comment)) {
                    return self::ACCESS_GRANTED;
                }
                break;
        }

        return  self::ACCESS_DENIED;
    }

    /**
     * @param User $user
     * @param comment $comment
     *
     * @return bool
     */
    protected function userHasAccess($user, $comment)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($this->userIsAuthor($user, $comment)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Comment $comment
     *
     * @return bool
     */
    protected function userIsAuthor($user, $comment)
    {
        return $user->getId() === $comment->getAuthor()->getId();
    }
}
