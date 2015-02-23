<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\IssueBundle\DataFixtures\ORM;

use Tracker\IssueBundle\Entity\Type;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTypeData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $typeBug = new Type();
        $typeBug->setValue(Type::TYPE_BUG);
        $manager->persist($typeBug);

        $typeStory = new Type();
        $typeStory->setValue(Type::TYPE_STORY);
        $manager->persist($typeStory);

        $typeSubTask = new Type();
        $typeSubTask->setValue(Type::TYPE_SUB_TASK);
        $manager->persist($typeSubTask);

        $typeTask = new Type();
        $typeTask->setValue(Type::TYPE_TASK);
        $manager->persist($typeTask);

        $manager->flush();

        $this->addReference("type.bug", $typeBug);
        $this->addReference("type.story", $typeStory);
        $this->addReference("type.subTask", $typeSubTask);
        $this->addReference("type.task", $typeTask);
    }
}
