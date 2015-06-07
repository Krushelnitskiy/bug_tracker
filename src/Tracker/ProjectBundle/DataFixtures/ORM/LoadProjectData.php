<?php

namespace Tracker\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Tracker\ProjectBundle\Entity\Project;
use Tracker\UserBundle\Entity\User;

class LoadProjectData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /**
         * @var $userOperator User
         */
        $userOperator = $this->getReference('user.operator');

        $project = new Project();
        $project->setLabel('First project');
        $project->setSummary('First project with test data');
        $project->addMember($userOperator);
        $project->setCreated(new \DateTime());
        $manager->persist($project);
        $manager->flush();

        $this->addReference('project.first', $project);
    }

    public function getDependencies()
    {
        return array(
            'Tracker\UserBundle\DataFixtures\ORM\LoadUserData'
        );
    }
}
