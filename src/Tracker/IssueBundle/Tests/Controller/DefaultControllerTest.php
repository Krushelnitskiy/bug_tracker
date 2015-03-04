<?php

namespace Tracker\IssueBundle\Tests\Controller;

use Tracker\TestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testViewList()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $crawler = $client->request('GET', '/issue');
        $crawler = $client->followRedirect();
        $this->assertContains($this->getReference('issue.story')->getSummary(), $crawler->html());
    }

    public function testShow()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $crawler = $client->request('GET', '/issue/'.$this->getReference('issue.story')->getId());
        $this->assertContains($this->getReference('issue.story')->getSummary(), $crawler->html());
    }

    public function testCreate()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $crawler = $client->request('GET', '/issue/new');

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_issuebundle_issue[project]'] = $this->getReference('project.first')->getId();
        $form['tracker_issuebundle_issue[type]'] = $this->getReference('type.story')->getId();
        $form['tracker_issuebundle_issue[summary]'] = 'issue test summary 1';
        $form['tracker_issuebundle_issue[priority]'] = $this->getReference('priority.trivial')->getId();
        $form['tracker_issuebundle_issue[code]'] = 'test-1';
        $form['tracker_issuebundle_issue[description]'] = 'issue test description 1';
        $form['tracker_issuebundle_issue[reporter]'] = $this->getReference('admin-user')->getId();
        $form['tracker_issuebundle_issue[assignee]'] = $this->getReference('admin-user')->getId();
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertContains('issue test summary 1', $crawler->html());
    }

    public function testEdit()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $issueId = $this->getReference('issue.story')->getId();
        $crawler = $client->request('GET', '/issue/'.$issueId.'/edit');

        $form = $crawler->selectButton('Update')->form();
        $form['tracker_issuebundle_issue[summary]'] = 'issue test summary 2';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertContains('issue test summary 2', $crawler->html());
    }
}
