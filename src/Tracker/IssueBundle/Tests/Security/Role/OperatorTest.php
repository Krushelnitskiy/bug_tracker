<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05.03.15
 * Time: 19:12
 */
namespace Tracker\IssueBundle\Tests\Security\Role;

use Symfony\Component\HttpFoundation\Response;
use Tracker\TestBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package Tracker\IssueBundle\Tests\Controller
 */
class OperatorTest extends WebTestCase
{

    public function testListIssue()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW' => 'test'
        ));

        $crawler = $client->request('GET', '/issue/');
        $this->assertContains('Unauthorised access!', $crawler->html());

        $crawler = $client->request('POST', '/issue/');
        $this->assertContains('Unauthorised access!', $crawler->html());
    }

    public function testCreateIssue()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/new');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testViewIssue()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/'.$this->getReference('issue.story')->getCode());
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testEditIssue()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/'.$this->getReference('issue.story')->getCode() .'/edit');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW' => 'test'
        ));

        $crawler = $client->request('PUT', '/issue/' . $this->getReference('issue.story')->getCode());
        $this->assertContains('Unauthorised access!', $crawler->html());
    }



    public function testCreateIssueNoProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/new');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testViewIssueNoProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/'.$this->getReference('issue.story')->getCode());
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testEditIssueNoProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/'.$this->getReference('issue.story')->getCode() .'/edit');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testIssueCreateComment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW' => 'test'
        ));

        $crawler = $client->request('POST', '/issue/comment/' . $this->getReference('issue.story')->getCode());
        $this->assertContains('Unauthorised access!', $crawler->html());
    }

    public function testCreateSubTask()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW' => 'test'
        ));

        $crawler = $client->request('GET', '/issue/' . $this->getReference('issue.story')->getCode().'/new');
        $this->assertContains('Unauthorised access!', $crawler->html());

        $crawler = $client->request('POST', '/issue/' . $this->getReference('issue.story')->getCode());
        $this->assertContains('Unauthorised access!', $crawler->html());
    }
}
