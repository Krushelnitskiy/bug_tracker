<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\IssueBundle\DataFixtures\ORM;

use Tracker\IssueBundle\Entity\Issue;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIssueData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issue = new Issue();
        $issue->setAssignee($this->getReference('admin-user'));
        $issue->setCode('1');
        $issue->setSummary('1');
        $issue->setDescription('');
        $issue->setCreated(new \DateTime());
        $issue->setUpdated(new \DateTime());
        $issue->setStatus($this->getReference('open'));

        $manager->persist($issue);


        $manager->flush();


    }
}
