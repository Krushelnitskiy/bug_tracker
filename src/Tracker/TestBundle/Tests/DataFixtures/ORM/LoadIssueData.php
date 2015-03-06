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
use Tracker\IssueBundle\Entity\Status;
use Tracker\IssueBundle\Entity\Type;
use Tracker\IssueBundle\Entity\Priority;
use Tracker\ProjectBundle\Entity\Project;
use Tracker\userBundle\Entity\User;

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
        /**
         * @var $user User
         */
        $user = $this->getReference('user.admin');
        /**
         * @var $status Status
         */
        $status = $this->getReference('status.open');
        /**
         * @var $type Type
         */
        $type = $this->getReference('type.story');
        /**
         * @var $priority Priority
         */
        $priority = $this->getReference('priority.trivial');
        /**
         * @var $project Project
         */
        $project =$this->getReference('project.first');
        /**
         * @var $userOperator User
         */
        $userOperator = $this->getReference('user.operator');

        $issueStory = new Issue();
        $issueStory->setAssignee($user);
        $issueStory->setCode('1');
        $issueStory->setSummary('issue test summary');
        $issueStory->setDescription('');
        $issueStory->setCreated(new \DateTime());
        $issueStory->setUpdated(new \DateTime());
        $issueStory->setStatus($status);
        $issueStory->setType($type);
        $issueStory->setPriority($priority);
        $issueStory->setProject($project);
        $issueStory->setReporter($userOperator);
        $manager->persist($issueStory);
        $manager->flush();

        $this->addReference('issue.story', $issueStory);
    }
}
