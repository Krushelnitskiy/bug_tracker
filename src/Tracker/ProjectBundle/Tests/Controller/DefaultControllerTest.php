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
        $this->assertContains($project->getLabel(), $crawler->html());

        $crawler = $client->request('GET', '/project/testtest');
        $this->assertContains('Unable to find Project entity.', $crawler->html());
    }

    public function testCreateWithOutAUth()
    {
        $client = static::createClient();
        $client->request('GET', '/project/new');
        $crawler = $client->followRedirect();

        $this->assertContains('Registration', $crawler->html());
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
        $this->assertContains('code111', $crawler->html());
    }

    public function testViewList()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/project');
        $crawler = $client->followRedirect();
        $this->assertContains('Create a new project', $crawler->html());
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

        $this->assertContains('label2222', $crawler->html());
    }

    public function testAccess()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/project');
        $crawler = $client->followRedirect();
        $this->assertContains('Unauthorised access!', $crawler->html());

        $client->request('GET', '/project/new');
        $this->assertContains('Unauthorised access!', $crawler->html());
    }
}
