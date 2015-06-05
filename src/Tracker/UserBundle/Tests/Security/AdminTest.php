<?php

namespace Tracker\UserBundle\Tests\Security;

use Symfony\Component\HttpFoundation\Response;

use Tracker\TestBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package Tracker\UserBundle\Tests\Security
 */
class AdminTest extends WebTestCase
{

    public function testCreateUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/user/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }


    public function testShowUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/user/test');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testEditUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'test'
        ));

        $client->request('GET', '/user/test/edit');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
