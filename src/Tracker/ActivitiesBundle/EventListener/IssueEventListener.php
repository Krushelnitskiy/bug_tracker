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

use Symfony\Component\DependencyInjection\ContainerInterface;

class IssueEventListener
{
    const CREATE_NEW_ISSUE = 'event.create_new_issue';
    const CHANGED_STATUS_TO = 'event.changed_status_to';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->container = $serviceContainer;
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
                $eventEntity->setEvent(self::CREATE_NEW_ISSUE);
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

            $user = $this->container->get('security.context')->getToken()->getUser();

            if ($eventArgs->hasChangedField('status')) {
                $eventEntity = new Activity();
                $eventEntity->setIssue($issue);
                $eventEntity->setProject($issue->getProject());
                $eventEntity->setUser($user);
                $eventEntity->setEvent(self::CHANGED_STATUS_TO);
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
