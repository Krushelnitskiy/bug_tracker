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
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIssueData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueStory = new Issue();
        $issueStory->setAssignee($this->getReference('admin-user'));
        $issueStory->setCode('1');
        $issueStory->setSummary('1');
        $issueStory->setDescription('');
        $issueStory->setCreated(new \DateTime());
        $issueStory->setUpdated(new \DateTime());
        $issueStory->setStatus($this->getReference('status.open'));
        $issueStory->setType($this->getReference('type.story'));
        $issueStory->setPriority($this->getReference('priority.trivial'));
        $issueStory->setProject($this->getReference('project.first'));
        $issueStory->setReporter($this->getReference('admin-user'));
        $issueStory->setAssignee($this->getReference('admin-user'));

        $manager->persist($issueStory);


        $issueStorySubTask = new Issue();
        $issueStorySubTask->setAssignee($this->getReference('admin-user'));
        $issueStorySubTask->setCode('2');
        $issueStorySubTask->setSummary('2');
        $issueStorySubTask->setDescription('');
        $issueStorySubTask->setCreated(new \DateTime());
        $issueStorySubTask->setUpdated(new \DateTime());
        $issueStorySubTask->setParent($issueStory);
        $issueStorySubTask->setStatus($this->getReference('status.open'));
        $issueStorySubTask->setType($this->getReference('type.subTask'));
        $issueStorySubTask->setPriority($this->getReference('priority.trivial'));
        $issueStorySubTask->setProject($this->getReference('project.first'));
        $issueStorySubTask->setReporter($this->getReference('admin-user'));
        $issueStorySubTask->setAssignee($this->getReference('admin-user'));

        $manager->persist($issueStorySubTask);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tracker\IssueBundle\DataFixtures\ORM\LoadPriorityData',
            'Tracker\IssueBundle\DataFixtures\ORM\LoadResolutionData',
            'Tracker\IssueBundle\DataFixtures\ORM\LoadStatusData',
            'Tracker\IssueBundle\DataFixtures\ORM\LoadTypeData',
            'Tracker\ProjectBundle\DataFixtures\ORM\LoadProjectData',
            'Tracker\UserBundle\DataFixtures\ORM\LoadUserData'
        );
    }
}
