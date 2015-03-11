<?php

namespace Tracker\IssueBundle\Tests\Controller;

use Tracker\TestBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package Tracker\IssueBundle\Tests\Controller
 */
class DefaultControllerTest extends WebTestCase
{

    public function testViewList()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/issue');
        $crawler = $client->followRedirect();
        self::assertContains($this->getReference('issue.story')->getSummary(), $crawler->html());
    }

    public function testShow()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/issue/'.$this->getReference('issue.story')->getId());
        self::assertContains($this->getReference('issue.story')->getSummary(), $crawler->html());
    }

    public function testCreate()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/issue/new');

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_issueBundle_issue[project]'] = $this->getReference('project.first')->getId();
        $form['tracker_issueBundle_issue[type]'] = $this->getReference('type.story')->getId();
        $form['tracker_issueBundle_issue[summary]'] = 'issue test summary 1';
        $form['tracker_issueBundle_issue[summary]'] = 'issue test summary 1';
        $form['tracker_issueBundle_issue[priority]'] = $this->getReference('priority.trivial')->getId();
        $form['tracker_issueBundle_issue[code]'] = 'test-1';
        $form['tracker_issueBundle_issue[description]'] = 'issue test description 1';
        $form['tracker_issueBundle_issue[reporter]'] = $this->getReference('user.manager')->getId();
        $form['tracker_issueBundle_issue[assignee]'] = $this->getReference('user.operator')->getId();
        $client->submit($form);
        $crawler = $client->followRedirect();

        self::assertContains('issue test summary 1', $crawler->html());
    }

    public function testEdit()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $issueId = $this->getReference('issue.story')->getId();
        $crawler = $client->request('GET', '/issue/'.$issueId.'/edit');

        $form = $crawler->selectButton('Update')->form();
        $form['tracker_issueBundle_issue[summary]'] = 'issue test summary 2';
        $form['tracker_issueBundle_issue[reporter]'] = $this->getReference('user.manager')->getId();
        $form['tracker_issueBundle_issue[status]'] = $this->getReference('status.inProgress')->getId();
        $client->submit($form);

        $crawler = $client->followRedirect();

        self::assertContains('manager', $crawler->html());
        self::assertContains('manager', $crawler->html());
        self::assertContains('In progress', $crawler->html());
    }

    public function testCreateComment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW'   => 'test'
        ));

        $issueId = $this->getReference('issue.story')->getId();
        $crawler = $client->request('GET', '/issue/'.$issueId);

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_issueBundle_comment_form[body]'] = 'issue test comment';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertContains('issue test comment', $crawler->html());

        $link = $crawler->filter('a:contains("Delete")')->eq(0)->link();
        $client->click($link);
        $crawler = $client->followRedirect();

        $this->assertEquals(0, $crawler->filter('a:contains("Delete")')->count());
    }

    public function testEditComment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW'   => 'test'
        ));

        $issueId = $this->getReference('issue.story')->getId();
        $crawler = $client->request('GET', '/issue/'.$issueId);

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_issueBundle_comment_form[body]'] = 'issue test comment';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertContains('issue test comment', $crawler->html());
        $link = $crawler->filter('.comment a:contains("Edit")')->link();
        $crawler= $client->click($link);
        $form = $crawler->selectButton('Update')->form();
        $form['tracker_issueBundle_comment_form[body]'] = 'issue test comment 222';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertContains('issue test comment 222', $crawler->html());
    }

    public function testCreateSubTask()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW'   => 'test'
        ));

        $issueId = $this->getReference('issue.story')->getId();
        $crawler = $client->request('GET', '/issue/'.$issueId);
        $link = $crawler->filter('a:contains("Create sub task")')->link();
        $crawler= $client->click($link);

        $form = $crawler->selectButton('Save')->form();
        $form['tracker_issueBundle_issueSubTask_form[summary]'] = 'issue test summary 1';
        $form['tracker_issueBundle_issueSubTask_form[priority]'] = $this->getReference('priority.trivial')->getId();
        $form['tracker_issueBundle_issueSubTask_form[code]'] = 'test-1';
        $form['tracker_issueBundle_issueSubTask_form[description]'] = 'issue test description 1';
        $form['tracker_issueBundle_issueSubTask_form[reporter]'] = $this->getReference('user.manager')->getId();
        $form['tracker_issueBundle_issueSubTask_form[assignee]'] = $this->getReference('user.operator')->getId();
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(0,$crawler->filter('a:contains("Delete")')->count());
    }
}
