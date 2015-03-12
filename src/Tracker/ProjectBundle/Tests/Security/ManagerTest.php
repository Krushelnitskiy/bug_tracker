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
class ManagerTest extends WebTestCase
{

    public function testListProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'manager',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/project');
        $crawler = $client->followRedirect();

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains($this->getReference('project.first')->getLabel(), $crawler->html());
    }

    public function testEditProject()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'manager',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/project/'.$this->getReference('project.first')->getId().'/edit');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains('Edit this project', $crawler->html());
    }
}
