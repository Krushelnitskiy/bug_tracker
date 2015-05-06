<?php

namespace Tracker\UserBundle\Tests\Security;

use Symfony\Component\HttpFoundation\Response;

use Tracker\TestBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package Tracker\UserBundle\Tests\Security
 */
class OperatorTest extends WebTestCase
{

    public function testListUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW' => 'test'
        ));

        $crawler = $client->request('GET', '/user/');
        $this->assertContains('Unauthorised access!', $crawler->html());
    }


    public function testCreateUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW' => 'test'
        ));

        $crawler = $client->request('GET', '/user/new');
        $this->assertContains('Unauthorised access!', $crawler->html());
    }

    public function testShowUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW' => 'test'
        ));

        $crawler = $client->request('GET', '/user/'.$this->getReference('user.operator')->getId());
        $this->assertContains('Unauthorised access!', $crawler->html());
    }


    public function testEditUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW' => 'test'
        ));

        $crawler = $client->request('GET', '/user/'.$this->getReference('user.operator')->getId().'/edit');
        $this->assertContains('Unauthorised access!', $crawler->html());
    }
}
