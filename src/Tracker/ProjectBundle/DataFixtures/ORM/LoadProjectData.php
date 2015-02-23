<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\ProjectBundle\DataFixtures\ORM;

use Tracker\ProjectBundle\Entity\Project;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProjectData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $project = new Project();
        $project->setCode('1');
        $project->setLabel('First project');
        $project->setSummary('First project with test data');

        $manager->persist($project);
        $manager->flush();

        $this->addReference("project.first", $project);
    }
}
