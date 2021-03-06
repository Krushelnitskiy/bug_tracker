<?php

namespace Tracker\ActivitiesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Tracker\ActivitiesBundle\Entity\Activity;
use Tracker\IssueBundle\Entity\Comment;

class CommentEventListener
{
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $comment = $eventArgs->getEntity();
        $entityManager = $eventArgs->getEntityManager();

        if ($comment instanceof Comment) {
            $issue = $comment->getIssue();

            $eventEntity = new Activity();
            $eventEntity->setIssue($issue);
            $eventEntity->setProject($issue->getProject());
            $eventEntity->setUser($comment->getAuthor());
            $eventEntity->setEvent(Activity::CREATED_COMMENT);
            $eventEntity->setCreated(new \DateTime());
            $entityManager->persist($eventEntity);
            $entityManager->flush();

            if (!$issue->getCollaborators()->contains($comment->getAuthor())) {
                $issue->addCollaborator($comment->getAuthor());
                $entityManager->persist($issue);
                $entityManager->flush();
            }
        }
    }
}
