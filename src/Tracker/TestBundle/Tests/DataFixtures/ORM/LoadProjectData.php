<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\TestBundle\Tests\DataFixtures\ORM;

use Tracker\ProjectBundle\Entity\Project;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tracker\UserBundle\Entity\User;

class LoadProjectData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /**
         * @var $userOperator User
         * @var $userManager User
         */
        $userOperator = $this->getReference('user.operator');
        $userManager = $this->getReference('user.manager');

        $project = new Project();
        $project->setCode('11');
        $project->setLabel('First project');
        $project->setSummary('First project with test data');
        $project->addMember($userOperator);
        $project->addMember($userManager);
        $project->setCreated(new \DateTime());
        $manager->persist($project);
        $manager->flush();

        $this->addReference('project.first', $project);
    }
}
