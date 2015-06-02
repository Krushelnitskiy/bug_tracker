<?php

namespace Tracker\ActivitiesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use Tracker\ActivitiesBundle\Entity\Activity;
use Tracker\IssueBundle\Entity\Issue;

class IssueEventListener
{
    /**
     * @var TokenStorage
     */
    private $securityTokenStorage;

    /**
     * @param TokenStorage $securityTokenStorage
     */
    public function __construct(TokenStorage $securityTokenStorage)
    {
        $this->securityTokenStorage = $securityTokenStorage;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $issue = $eventArgs->getEntity();
        if ($issue instanceof Issue) {
            if (!$issue->getCollaborators()->contains($issue->getReporter())) {
                $issue->getCollaborators()->add($issue->getReporter());
            }

            if (!$issue->getCollaborators()->contains($issue->getAssignee())) {
                $issue->getCollaborators()->add($issue->getAssignee());
            }
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $issue = $eventArgs->getEntity();
        $entityManager = $eventArgs->getEntityManager();

        if ($issue instanceof Issue) {
                $eventEntity = new Activity();
                $eventEntity->setIssue($issue);
                $eventEntity->setProject($issue->getProject());
                $eventEntity->setUser($issue->getReporter());
                $eventEntity->setEvent(Activity::CREATE_NEW_ISSUE);
                $eventEntity->setCreated(new \DateTime());
                $entityManager->persist($eventEntity);
                $entityManager->flush();
        }
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $issue = $eventArgs->getEntity();
        $entityManager = $eventArgs->getEntityManager();

        if ($issue instanceof Issue) {
            if ($this->hasChangedReporter($eventArgs)) {
                $issue->getCollaborators()->add($issue->getReporter());
            }

            if ($this->hasChangedAssignee($eventArgs)) {
                $issue->getCollaborators()->add($issue->getAssignee());
            }

            $user = $this->securityTokenStorage->getToken()->getUser();

            if ($eventArgs->hasChangedField('status')) {
                $eventEntity = new Activity();
                $eventEntity->setIssue($issue);
                $eventEntity->setProject($issue->getProject());
                $eventEntity->setUser($user);
                $eventEntity->setEvent(Activity::CHANGED_STATUS_TO);
                $eventEntity->setCreated(new \DateTime());
                $entityManager->persist($eventEntity);

                if (!$issue->getCollaborators()->contains($user)) {
                    $issue->getCollaborators()->add($user);
                }
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

    /**
     * @param PreUpdateEventArgs $eventArgs
     * @return bool
     */
    private function hasChangedAssignee(PreUpdateEventArgs $eventArgs)
    {
        $issue = $eventArgs->getEntity();

        return $eventArgs->hasChangedField('assignee') && !$issue->getCollaborators()->contains($issue->getAssignee());
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     * @return bool
     */
    private function hasChangedReporter(PreUpdateEventArgs $eventArgs)
    {
        $issue = $eventArgs->getEntity();

        return $eventArgs->hasChangedField('reporter') && !$issue->getCollaborators()->contains($issue->getReporter());
    }
}
