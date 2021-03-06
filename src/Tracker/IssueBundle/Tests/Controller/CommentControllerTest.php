<?php

namespace Tracker\IssueBundle\Tests\Controller;

use Tracker\TestBundle\Test\WebTestCase;

/**
 * Class CommentControllerTest
 * @package Tracker\IssueBundle\Tests\Controller
 */
class CommentControllerTest extends WebTestCase
{

    public function testAdminCreateComment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/issue/'.$this->getReference('issue.story')->getCode());

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_issueBundle_comment_form[body]'] = 'You need add description.';
        $client->followRedirects();
        $client->submit($form);
        $crawler = $client->getCrawler();

        $html = $crawler->html();
        self::assertContains('Delete', $html);
        self::assertContains('You need add description.', $html);
    }

    public function testReporterCreateComment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/issue/'.$this->getReference('issue.story')->getCode());

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_issueBundle_comment_form[body]'] = 'You need add description.';
        $client->followRedirects();
        $client->submit($form);
        $crawler = $client->getCrawler();

        $html = $crawler->html();
        self::assertContains('Delete', $html);
        self::assertContains('You need add description.', $html);
    }
}
