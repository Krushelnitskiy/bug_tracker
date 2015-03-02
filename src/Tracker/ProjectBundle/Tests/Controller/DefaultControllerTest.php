<?php

namespace Tracker\ProjectBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Loader;
use Tracker\ProjectBundle\Tests\DataFixtures\ORM\LoadProjectData;
use Tracker\ProjectBundle\Tests\DataFixtures\ORM\LoadUserData;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class DefaultControllerTest extends WebTestCase
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

    public function testShow()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $crawler = $client->request('GET', '/project/'.$this->getReference('project.first')->getId());
        $this->assertContains($this->getReference('project.first')->getLabel(), $crawler->html());
    }

    public function testCreateWithOutAUth()
    {
        $client = static::createClient();
        $client->request('GET', '/project/new');
        $crawler = $client->followRedirect();

        $this->assertContains('Registration', $crawler->html());
    }
//
    public function testCreate()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $crawler = $client->request('GET', '/project/new');

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_projectbundle_project[label]'] = 'label';
        $form['tracker_projectbundle_project[summary]']='summary';
        $form['tracker_projectbundle_project[code]']= 'code111';

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertContains('code111', $crawler->html());
    }

    public function testViewList()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $client->request('GET', '/project');
        $crawler = $client->followRedirect();
        $this->assertContains('Create a new project', $crawler->html());
    }

    public function testEdit()
    {
//        $this->getReference('admin-user')->getUsername();
//        $this->getReference('admin-user')->getUsername();
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $projectId = $this->getReference('project.first')->getId();
        $crawler = $client->request('GET', '/project/'.$projectId.'/edit');

        $form = $crawler->selectButton('Update')->form();
        $form['tracker_projectbundle_project[label]'] = 'label2222';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertContains('label2222', $crawler->html());
    }

//    public function testIndex()
//    {
        // Create a new client to browse the application
//        $client = static::createClient();
//
//         Create a new entry in the database
//        $crawler = $client->request('GET', '/project/');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /project/");
//
//        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
//         Fill in the form and submit it
//        $form = $crawler->selectButton('Create')->form(array(
//            'tracker_projectbundle_project[label]'  => 'label',
//            'tracker_projectbundle_project[summary]'  => 'summary',
//            'tracker_projectbundle_project[code]'  => 'code'
//             ... other fields to fill
//        ));
//        $form['tracker_projectbundle_project[members]']->select(array('25'));
//
//
//        $crawler = $client->followRedirect();
//        $client->submit($form);
//
//
//
        // Check data in the show view
//        $this->assertGreaterThan(0, $crawler->filter('td:contains("label")')->count(), 'Missing element td:contains("label")');

//        // Edit the entity
//        $crawler = $client->click($crawler->selectLink('Edit')->link());
//
//        $form = $crawler->selectButton('Update')->form(array(
//            'tracker_projectbundle_project[label]'  => 'Foo',
//             ... other fields to fill
//        ));

//        $client->submit($form);
//        $crawler = $client->followRedirect();
//
//         Check the element contains an attribute with value equals "Foo"
//        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');
//
//        // Delete the entity
//        $client->submit($crawler->selectButton('Delete')->form());
//        $crawler = $client->followRedirect();
//
//        // Check the entity has been delete on the list
//        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
//    }
}
