<?php

namespace Tracker\ProjectBundle\Tests\Controller;

use Tracker\TestBundle\Test\WebTestCase;
use Tracker\ProjectBundle\Entity\Project;

class DefaultControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        /**
         * @var $project Project
         */
        $project = $this->getReference('project.first');

        $crawler = $client->request('GET', '/project/'.$project->getId());
        self::assertContains($project->getLabel(), $crawler->html());

        $crawler = $client->request('GET', '/project/testtest');
        self::assertContains('Unable to find Project entity.', $crawler->html());
    }

    public function testCreateWithOutAUth()
    {
        $client = static::createClient();
        $client->request('GET', '/project/new');
        $crawler = $client->followRedirect();

        self::assertContains('Registration', $crawler->html());
    }

    public function testCreate()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));
        $crawler = $client->request('GET', '/project/new');

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_projectBundle_project[label]'] = 'label';
        $form['tracker_projectBundle_project[summary]']='summary';
        $form['tracker_projectBundle_project[code]']= 'code111';

        $client->submit($form);
        $crawler = $client->followRedirect();
        self::assertContains('code111', $crawler->html());
    }

    public function testViewList()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/project');
        $crawler = $client->followRedirect();
        self::assertContains('Create a new project', $crawler->html());
    }

    public function testEdit()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        /**
         * @var $project Project
         */
        $project = $this->getReference('project.first');

        $crawler = $client->request('GET', '/project/'.$project->getId().'/edit');

        $form = $crawler->selectButton('Update')->form();
        $form['tracker_projectBundle_project[label]'] = 'label2222';
        $client->submit($form);

        $crawler = $client->followRedirect();

        self::assertContains('label2222', $crawler->html());
    }

    public function testAccess()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/project');
        $crawler = $client->followRedirect();
        self::assertContains('Unauthorised access!', $crawler->html());

        $client->request('GET', '/project/new');
        self::assertContains('Unauthorised access!', $crawler->html());

        $client->request('GET', '/project/2231');
        self::assertContains('Unauthorised access!', $crawler->html());

        $client->request('GET', '/project/2231/edit');
        self::assertContains('Unauthorised access!', $crawler->html());

        $client->request('GET', '/project/'.$this->getReference('project.first')->getId().'/edit');
        self::assertContains('Unauthorised access!', $crawler->html());
    }
}
