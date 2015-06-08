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

        $crawler = $client->request('GET', '/project/'.$project->getCode());
        self::assertContains($project->getLabel(), $crawler->html());

        $crawler = $client->request('GET', '/project/testtest');
        self::assertContains('TrackerProjectBundle:Project object not found.', $crawler->html());
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

        $client->submit($form);
        $crawler = $client->followRedirect();
        self::assertContains('summary', $crawler->html());
    }

    public function testViewList()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->followRedirects();
        $client->request('GET', '/project');
        $crawler =$client->getCrawler();
            self::assertContains('Create project', $crawler->html());
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
        $crawler = $client->request('GET', '/project/'.$project->getCode().'/edit');

        $form = $crawler->selectButton('Update')->form();
        $form['tracker_projectBundle_project[label]'] = 'label2222';
        $client->followRedirects();
        $client->submit($form);

        $crawler  = $client->getCrawler();

        self::assertContains('label2222', $crawler->html());
    }
}
