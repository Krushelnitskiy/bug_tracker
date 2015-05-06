<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Tracker\IssueBundle\Entity\Issue;
use Tracker\IssueBundle\Entity\Type;
use Tracker\IssueBundle\Entity\Priority;
use Tracker\IssueBundle\Entity\Status;
use Tracker\ProjectBundle\Entity\Project;
use Tracker\UserBundle\Entity\User;

class LoadIssueData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /**
         * @var $user User
         * @var $project Project
         * @var $priorityTrivial Priority
         * @var $status Status
         * @var $typeStory Type
         * @var $typeSubTask Type
         */
        $user = $this->getReference('admin-user');
        $project = $this->getReference('project.first');
        $priorityTrivial = $this->getReference('priority.trivial');
        $status = $this->getReference('status.open');
        $typeStory = $this->getReference('type.story');
        $typeSubTask = $this->getReference('type.subTask');

        $issueStory = new Issue();
        $issueStory->setAssignee($user);
        $issueStory->setCode('1');
        $issueStory->setSummary('1');
        $issueStory->setDescription('');
        $issueStory->setCreated(new \DateTime());
        $issueStory->setUpdated(new \DateTime());
        $issueStory->setStatus($status);
        $issueStory->setType($typeStory);
        $issueStory->setPriority($priorityTrivial);
        $issueStory->setProject($project);
        $issueStory->setReporter($user);
        $issueStory->setAssignee($user);

        $manager->persist($issueStory);

        $issueStorySubTask = new Issue();
        $issueStorySubTask->setAssignee($user);
        $issueStorySubTask->setCode('2');
        $issueStorySubTask->setSummary('2');
        $issueStorySubTask->setDescription('');
        $issueStorySubTask->setCreated(new \DateTime());
        $issueStorySubTask->setUpdated(new \DateTime());
        $issueStorySubTask->setParent($issueStory);
        $issueStorySubTask->setStatus($status);
        $issueStorySubTask->setType($typeSubTask);
        $issueStorySubTask->setPriority($priorityTrivial);
        $issueStorySubTask->setProject($project);
        $issueStorySubTask->setReporter($user);
        $issueStorySubTask->setAssignee($user);

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
