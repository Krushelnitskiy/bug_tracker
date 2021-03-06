<?php

namespace Tracker\IssueBundle\Tests\Security\Role;

use Symfony\Component\HttpFoundation\Response;
use Tracker\TestBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package Tracker\IssueBundle\Tests\Controller
 */
class ManagerTest extends WebTestCase
{

    public function testCreateIssue()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'manager',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/new');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testViewIssue()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'manager',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/'.$this->getReference('issue.story')->getCode());
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testEditIssue()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'manager',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/issue/'.$this->getReference('issue.story')->getCode() .'/edit');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
