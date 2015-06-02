<?php

namespace Tracker\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Tracker\IssueBundle\Entity\Resolution;

class LoadResolutionData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $resolutionDone = new Resolution();
        $resolutionDone->setValue(Resolution::RESOLUTION_DONE);
        $manager->persist($resolutionDone);

        $resolutionFixed = new Resolution();
        $resolutionFixed->setValue(Resolution::RESOLUTION_FIXED);
        $manager->persist($resolutionFixed);

        $resolutionWontDo = new Resolution();
        $resolutionWontDo->setValue(Resolution::RESOLUTION_WONT_DO);
        $manager->persist($resolutionWontDo);

        $resolutionWontFix = new Resolution();
        $resolutionWontFix->setValue(Resolution::RESOLUTION_WONT_FIX);
        $manager->persist($resolutionWontFix);

        $manager->flush();

        $this->addReference('resolution.done', $resolutionDone);
        $this->addReference('resolution.fixed', $resolutionFixed);
        $this->addReference('resolution.wontDo', $resolutionWontDo);
        $this->addReference('resolution.wontFix', $resolutionWontFix);
    }
}
