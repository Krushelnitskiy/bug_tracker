<?php

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
         * @var $userOperator User
         * @var $userManager User
         * @var $project Project
         * @var $priorityTrivial Priority
         * @var $status Status
         * @var $typeStory Type
         * @var $typeSubTask Type
         */
        $user = $this->getReference('admin-user');
        $userManager = $this->getReference('user.manager');
        $userOperator = $this->getReference('user.operator');
        $project = $this->getReference('project.first');
        $priorityTrivial = $this->getReference('priority.trivial');
        $status = $this->getReference('status.open');
        $typeStory = $this->getReference('type.story');
        $typeSubTask = $this->getReference('type.subTask');

        $issueStory = new Issue();
        $issueStory->setAssignee($user);
        $issueStory->setCode(strtoupper('story-1'));
        $issueStory->setSummary('Bug Tracker');
        $issueStory->setDescription('');
        $issueStory->setCreated(new \DateTime());
        $issueStory->setUpdated(new \DateTime());
        $issueStory->setStatus($status);
        $issueStory->setType($typeStory);
        $issueStory->setPriority($priorityTrivial);
        $issueStory->setProject($project);
        $issueStory->setReporter($userManager);
        $issueStory->setAssignee($userOperator);

        $manager->persist($issueStory);

        $data = $this->getData();

        foreach ($data as $item) {
            $issueStorySubTask = new Issue();
            $issueStorySubTask->setAssignee($user);
            $issueStorySubTask->setCode(strtoupper($item['code']));
            $issueStorySubTask->setSummary($item['summary']);
            $issueStorySubTask->setDescription('');
            $issueStorySubTask->setCreated(new \DateTime());
            $issueStorySubTask->setUpdated(new \DateTime());
            $issueStorySubTask->setParent($issueStory);
            $issueStorySubTask->setStatus($status);
            $issueStorySubTask->setType($typeSubTask);
            $issueStorySubTask->setPriority($priorityTrivial);
            $issueStorySubTask->setProject($project);
            $issueStorySubTask->setReporter($userManager);
            $issueStorySubTask->setAssignee($userOperator);

            $manager->persist($issueStorySubTask);
        }

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

    protected function getData()
    {
        return [[
            'code' => 'task-1',
            'summary' => 'Create empty project'
        ],
            [
                'code' => 'task-2',
                'summary' => 'Create Bundles UserBundle, IssueBundle, ProjectBundle, ActivitiesBundle, HomeBundle.'
            ],
            [
                'code' => 'task-3',
                'summary' => 'Crete Entities in bundles and add doctrine annotate'
            ],
            [
                'code' => 'task-4',
                'summary' => 'Add FosUserBundle'
            ],
            [
                'code' => 'task-5',
                'summary' => 'Create page with form login'
            ],
            [
                'code' => 'task-6',
                'summary' => 'Create pages with registration form, forgot password'
            ], [
                'code' => 'task-7',
                'summary' => 'Create data Fixtures for demonstration a bug tracker'
            ], [
                'code' => 'task-8',
                'summary' => 'Create User Page'
            ], [
                'code' => 'task-9',
                'summary' => 'Create Issue page'
            ], [
                'code' => 'task-10',
                'summary' => 'Create project page'
            ], [
                'code' => 'task-11',
                'summary' => 'Create Main page'
            ], [
                'code' => 'task-12',
                'summary' => 'Create page edit profile'
            ], [
                'code' => 'task-13',
                'summary' => 'Create page create / edit project'
            ], [
                'code' => 'task-14',
                'summary' => 'Create page create / edit issues'
            ], [
                'code' => 'task-15',
                'summary' => 'Add saving activities of events: Create issue, Change status of issue, Comment in issue'
            ], [
                'code' => 'task-16',
                'summary' => 'Add notifications via email for Issue collaborators about events'
            ], [
                'code' => 'task-19',
                'summary' => 'Create User roles: Administrator, Manager, Operator'
            ], [
                'code' => 'task-20',
                'summary' => 'Apply permision by roles'
            ], [
                'code' => 'task-21',
                'summary' => 'Delete comments'
            ], [
                'code' => 'task-22',
                'summary' => 'Edit Comments'
            ], [
                'code' => 'task-23',
                'summary' => 'Fix code review'
            ]
        ];
    }
}
