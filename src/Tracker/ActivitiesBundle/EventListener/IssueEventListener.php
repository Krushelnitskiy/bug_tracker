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
use Tracker\IssueBundle\Entity\Issue;

class IssueEventListener
{
    /** @PostPersist */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $issue = $eventArgs->getEntity();
        $entityManager = $eventArgs->getEntityManager();

        if ($issue instanceof Issue) {
                $eventEntity = new Activity();
                $eventEntity->setIssue($issue);
                $eventEntity->setProject($issue->getProject());
                $eventEntity->setUser($issue->getReporter());
                $eventEntity->setEvent($issue->getStatus()->getValue());
                $entityManager->persist($eventEntity);
                $entityManager->flush();

        }
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $issue = $eventArgs->getEntity();
        $entityManager = $eventArgs->getEntityManager();

        if ($issue instanceof Issue) {
            if ($eventArgs->hasChangedField('status')) {
                $eventEntity = new Activity();
                $eventEntity->setIssue($issue);
                $eventEntity->setProject($issue->getProject());
                $eventEntity->setUser($issue->getReporter());
                $eventEntity->setEvent($issue->getStatus()->getValue());
                $eventEntity->setEvent($eventArgs->getNewValue('status'));
                $entityManager->persist($eventEntity);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $event->getEntityManager()->flush();
    }
}
