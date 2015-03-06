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
use Symfony\Component\Security\Core\User\UserInterface;

use Tracker\IssueBundle\Entity\Comment;
use Tracker\UserBundle\Entity\User;

class CommentVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const DELETE = 'delete';

    /**
     * @param string $attribute
     * @return bool
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
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Tracker\IssueBundle\Entity\Comment';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param Comment $comment
     * @param array $attributes
     * @return int
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
        if (!$user instanceof UserInterface) {
            return self::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::EDIT:
                if ($this->userCanEdit($user, $comment)) {
                    return self::ACCESS_GRANTED;
                }
                break;
            case self::DELETE:
                if ($this->userCanDelete($user, $comment)) {
                    return self::ACCESS_GRANTED;
                }
                break;
        }

        return  self::ACCESS_DENIED;
    }

    /**
     * @param User $user
     * @param comment $comment
     * @return bool
     */
    public function userCanEdit($user, $comment)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        $userIsAuthor = $this->userIsAuthor($user, $comment);

        if ($userIsAuthor) {
            return true;
        }

        return false;
    }

//    public function userCanCreate(User $user, $issue)
//    {
//        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_ADMINISTRATOR')) {
//            return true;
//        }
//        if ($user->hasRole('ROLE_MANAGER')) {
//            return true;
//        }
//
//        if ($user->hasRole("ROLE_OPERATOR") && $issue->getProject()->getMembers()->contains($user)) {
//            return true;
//        }
//
//
//        return false;
//    }


    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function userCanDelete($user, $comment)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        $userIsAuthor = $this->userIsAuthor($user, $comment);

        if ($userIsAuthor) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    protected function userIsAuthor($user, $comment)
    {
        return $user->getId() === $comment->getAuthor()->getId();
    }
}
