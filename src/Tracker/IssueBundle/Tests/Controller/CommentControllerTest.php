<?php

namespace Tracker\IssueBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Tracker\TestBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{

    public function testViewList()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/issue/'.$this->getReference('issue.story')->getId());
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /*
        public function testCompleteScenario()
        {
            // Create a new client to browse the application
            $client = static::createClient();

            // Go to the list view
            $crawler = $client->request('GET', '/comment/');
            $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /comment/");

            // Go to the show view
            $crawler = $client->click($crawler->selectLink('show')->link());
            $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        }
    */
}
