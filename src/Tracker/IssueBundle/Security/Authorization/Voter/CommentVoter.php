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

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::CREATE,
            self::DELETE
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'Tracker\IssueBundle\Entity\Comment';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @var \Tracker\ProjectBundle\Entity\Project $comment
     */
    public function vote(TokenInterface $token, $comment, array $attributes)
    {

        if (!$this->supportsClass(get_class($comment))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException('Only one attribute is allowed for VIEW or EDIT or CREATE');
        }

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
//            case self::VIEW:
//                if ($this->userCanView($user, $comment)) {
//                    return VoterInterface::ACCESS_GRANTED;
//                }
//                break;
//            case self::CREATE:
//                if ($this->userCanCreate($user, $comment)) {
//                    return VoterInterface::ACCESS_GRANTED;
//                }
//                break;
//            case self::EDIT:
//                if ($this->userCanEdit($user, $comment)) {
//                    return VoterInterface::ACCESS_GRANTED;
//                }
//                break;
            case self::DELETE:
                if ($this->userCanDelete($user, $comment)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return  VoterInterface::ACCESS_DENIED;
    }

//    public function userCanView(User $user, $issue)
//    {
//        if ($user->hasRole("ROLE_ADMIN") || $user->hasRole('ROLE_ADMINISTRATOR')) {
//            return true;
//        }
//
//        if ($user->hasRole("ROLE_MANAGER")) {
//            return true;
//        }
//
//        if ($user->hasRole("ROLE_OPERATOR") && $issue->getProject()->getMembers()->contains($user)) {
//            return true;
//        }
//
//        return false;
//    }
//
//    public function userCanEdit(User $user, $issue)
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
//
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


    public function userCanDelete(User $user, Comment $comment)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($user->getId() === $comment->getAuthor()->getId()) {
            return true;
        }

        return false;
    }

}
