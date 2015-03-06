<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\IssueBundle\DataFixtures\ORM;

use Tracker\IssueBundle\Entity\Priority;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPriorityData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $priorityTrivial = new Priority();
        $priorityTrivial->setValue(Priority::PRIORITY_TRIVIAL);
        $manager->persist($priorityTrivial);

        $priorityMinor = new Priority();
        $priorityMinor->setValue(Priority::PRIORITY_MINOR);
        $manager->persist($priorityMinor);

        $priorityMajor = new Priority();
        $priorityMajor->setValue(Priority::PRIORITY_MAJOR);
        $manager->persist($priorityMajor);

        $priorityCritical = new Priority();
        $priorityCritical->setValue(Priority::PRIORITY_CRITICAL);
        $manager->persist($priorityCritical);

        $priorityBlocker = new Priority();
        $priorityBlocker->setValue(Priority::PRIORITY_BLOCKER);
        $manager->persist($priorityBlocker);

        $manager->flush();

        $this->addReference('priority.trivial', $priorityTrivial);
        $this->addReference('priority.minor', $priorityMinor);
        $this->addReference('priority.major', $priorityMajor);
        $this->addReference('priority.critical', $priorityCritical);
        $this->addReference('priority.blocker', $priorityBlocker);
    }
}
