<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05.03.15
 * Time: 19:12
 */
namespace Tracker\ProjectBundle\Tests\Security;

use Symfony\Component\HttpFoundation\Response;
use Tracker\TestBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package Tracker\ProjectBundle\Tests\Controller
 */
class OperatorTest extends WebTestCase
{

    public function testListProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/project');
        $crawler = $client->followRedirect();
        $this->assertContains('Unauthorised access!', $crawler->html());

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/project');
        $crawler = $client->followRedirect();
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains($this->getReference('project.first')->getLabel(), $crawler->html());

    }

    public function testCreateProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/project/new');
        $this->assertContains('Unauthorised access!', $crawler->html());

        $crawler = $client->request('POST', '/project/');
        $this->assertContains('Unauthorised access!', $crawler->html());
    }

    public function testViewProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/project/'.$this->getReference('project.first')->getId());
        $this->assertContains('Unauthorised access!', $crawler->html());

        $client->request('GET', '/project/2231');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }


    public function testUpdateProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator.noProjects',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/project/'.$this->getReference('project.first')->getId().'/edit');
        $this->assertContains('Unauthorised access!', $crawler->html());

        $crawler = $client->request('PUT', '/project/'.$this->getReference('project.first')->getId());
        $this->assertContains('Unauthorised access!', $crawler->html());
    }


}
