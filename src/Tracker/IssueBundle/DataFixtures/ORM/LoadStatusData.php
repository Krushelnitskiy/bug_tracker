<?php

namespace Tracker\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Tracker\IssueBundle\Entity\Status;

class LoadStatusData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $statusOpen = new Status();
        $statusOpen->setValue(Status::STATUS_OPEN);
        $manager->persist($statusOpen);

        $statusClosed = new Status();
        $statusClosed->setValue(Status::STATUS_CLOSED);
        $manager->persist($statusClosed);

        $statusInProgress = new Status();
        $statusInProgress->setValue(Status::STATUS_IN_PROGRESS);
        $manager->persist($statusInProgress);

        $manager->flush();

        $this->addReference('status.open', $statusOpen);
        $this->addReference('status.closed', $statusClosed);
        $this->addReference('status.inProgress', $statusInProgress);
    }
}
