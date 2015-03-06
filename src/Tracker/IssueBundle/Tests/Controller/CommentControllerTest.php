<?php

namespace Tracker\IssueBundle\Tests\Controller;

use Tracker\TestBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{

    public function testAdminCreateComment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/issue/'.$this->getReference('issue.story')->getId());

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_issueBundle_comment[body]'] = 'You need add description.';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $html = $crawler->html();
        $this->assertContains('Delete', $html);
        $this->assertContains('You need add description.', $html);
    }

    public function testReporterCreateComment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/issue/'.$this->getReference('issue.story')->getId());

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_issueBundle_comment[body]'] = 'You need add description.';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $html = $crawler->html();
        $this->assertContains('Delete', $html);
        $this->assertContains('You need add description.', $html);
    }
}
