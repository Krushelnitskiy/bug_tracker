<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.02.15
 * Time: 17:25
 */

namespace Tracker\ActivitiesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tracker\ActivitiesBundle\Entity\Activity;
use Tracker\IssueBundle\Entity\Comment;
use Tracker\IssueBundle\Entity\Issue;

class CommentEventListener
{

    /** @PostPersist */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $comment = $eventArgs->getEntity();
        $entityManager = $eventArgs->getEntityManager();

        if ($comment instanceof Comment) {
            $issue = $comment->getIssue();

            $eventEntity = new Activity();
            $eventEntity->setIssue($issue);
            $eventEntity->setProject($issue->getProject());
            $eventEntity->setUser($issue->getReporter());
            $eventEntity->setEvent($issue->getStatus()->getValue());
            $entityManager->persist($eventEntity);
            $entityManager->flush();

            if (!$issue->getCollaborators()->contains($comment->getAuthor())) {
                $issue->addCollaborator($comment->getAuthor());
                $entityManager->persist($eventEntity);
                $entityManager->flush();
            }
        }
    }
}
