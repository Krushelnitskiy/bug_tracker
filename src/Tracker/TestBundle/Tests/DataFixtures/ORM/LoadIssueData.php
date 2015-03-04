<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\TestBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tracker\IssueBundle\Entity\Issue;
use Tracker\UserBundle\Entity\User;

// implements OrderedFixtureInterface

class LoadIssueData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueStory = new Issue();
        $issueStory->setAssignee($this->getReference('admin-user'));
        $issueStory->setCode('1');
        $issueStory->setSummary('issue test summary');
        $issueStory->setDescription('');
        $issueStory->setCreated(new \DateTime());
        $issueStory->setUpdated(new \DateTime());
        $issueStory->setStatus($this->getReference('status.open'));
        $issueStory->setType($this->getReference('type.story'));
        $issueStory->setPriority($this->getReference('priority.trivial'));
        $issueStory->setProject($this->getReference('project.first'));
        $manager->persist($issueStory);
        $manager->flush();

        $this->addReference('issue.story', $issueStory);
    }
}
