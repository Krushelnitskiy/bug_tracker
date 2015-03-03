<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.03.15
 * Time: 12:47
 */

namespace Tracker\TestBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Tracker\ProjectBundle\Tests\DataFixtures\ORM\LoadProjectData;
use Tracker\ProjectBundle\Tests\DataFixtures\ORM\LoadUserData;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class WebTestCase extends BaseWebTestCase
{
    protected $referenceRepository;

    public function setUp()
    {
        $client = self::createClient();
        $container = $client->getKernel()->getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        // Purge tables
        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->purge();

        $loader = new Loader();

        $fixtures = new LoadUserData();
        $fixtures->setContainer($container);
        $loader->addFixture($fixtures);

        $fixtures = new LoadProjectData();
        $loader->addFixture($fixtures);

        $executor->execute($loader->getFixtures());
        $this->referenceRepository = $executor->getReferenceRepository();
    }

    protected function getReference($referenceUID)
    {
        return $this->referenceRepository->getReference($referenceUID);
    }
}
