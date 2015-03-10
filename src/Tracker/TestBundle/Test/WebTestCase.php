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
use Tracker\IssueBundle\DataFixtures\ORM\LoadPriorityData;
use Tracker\IssueBundle\DataFixtures\ORM\LoadResolutionData;
use Tracker\IssueBundle\DataFixtures\ORM\LoadStatusData;
use Tracker\IssueBundle\DataFixtures\ORM\LoadTypeData;
use Tracker\TestBundle\Tests\DataFixtures\ORM\LoadCommentData;
use Tracker\TestBundle\Tests\DataFixtures\ORM\LoadIssueData;
use Tracker\TestBundle\Tests\DataFixtures\ORM\LoadProjectData;
use Tracker\TestBundle\Tests\DataFixtures\ORM\LoadUserData;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\Tools\SchemaTool;

class WebTestCase extends BaseWebTestCase
{
    /**
     * @var $referenceRepository ReferenceRepository
     */
    protected $referenceRepository;

    public function setUp()
    {


        $client = self::createClient();
        $container = $client->getKernel()->getContainer();
        $entityManager = $container->get('doctrine')->getManager();

//        $schemaTool = new SchemaTool($entityManager);
//        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
//        $schemaTool->dropSchema($metadata);
//        $schemaTool->createSchema($metadata);

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

        $fixtures = new LoadPriorityData();
        $loader->addFixture($fixtures);

        $fixtures = new LoadResolutionData();
        $loader->addFixture($fixtures);

        $fixtures = new LoadStatusData();
        $loader->addFixture($fixtures);

        $fixtures = new LoadTypeData();
        $loader->addFixture($fixtures);

        $fixtures = new LoadIssueData();
        $fixtures->setContainer($container);
        $loader->addFixture($fixtures);

//        $fixtures = new LoadCommentData();
//        $fixtures->setContainer($container);
//        $loader->addFixture($fixtures);

        $executor->execute($loader->getFixtures());
        $this->referenceRepository = $executor->getReferenceRepository();
    }

    protected function getReference($referenceUID)
    {
        return $this->referenceRepository->getReference($referenceUID);
    }
}
