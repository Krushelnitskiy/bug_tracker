<?php

namespace Tracker\ActivitiesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Tracker\ActivitiesBundle\Entity\Activity;
use Tracker\UserBundle\Entity\User;

class ActivityEventListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

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
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $activity = $eventArgs->getEntity();

        if ($activity instanceof Activity) {
            $collaborateEmails = $activity->getIssue()->getCollaborators()->map(function (User $collaborate) {
                return $collaborate->getEmail();
            })->getValues();

            $mailer = $this->container->get('mailer');

            $message = $mailer->createMessage()
                ->setSubject('Activity by Issue:' . $activity->getIssue()->getSummary())
                ->setFrom('send@example.com')
                ->setTo($collaborateEmails)
                ->setBody(
                    $this->container->get('twig')->render(
                        'TrackerActivitiesBundle:Emails:eventOfIssue.html.twig',
                        array('activity' => $activity)
                    ),
                    'text/html'
                );

            $mailer->send($message);
        }
    }
}
