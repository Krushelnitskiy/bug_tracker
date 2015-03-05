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

    /** @PostPersist */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $issue = $eventArgs->getEntity();
        $entityManager = $eventArgs->getEntityManager();

        if ($issue instanceof Issue) {
//            var_dump($this->container->get('security.context')->getToken());exit;
//                $user = $this->container->get('security.context')->getToken()->getUser();

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
            if ($eventArgs->hasChangedField('reporter') && !$issue->getCollaborators()->contains($issue->getReporter())) {
                $issue->getCollaborators()->add($issue->getReporter());
            }

            if ($eventArgs->hasChangedField('assignee') && !$issue->getCollaborators()->contains($issue->getAssignee())) {
                $issue->getCollaborators()->add($issue->getAssignee());
            }

            $user = $this->container->get('security.context')->getToken()->getUser();

            if ($eventArgs->hasChangedField('status')) {
                $eventEntity = new Activity();
                $eventEntity->setIssue($issue);
                $eventEntity->setProject($issue->getProject());
                $eventEntity->setUser($user);
//                $eventEntity->setEvent($issue->getStatus()->getValue());
                $eventEntity->setEvent($eventArgs->getNewValue('status'));
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
}
