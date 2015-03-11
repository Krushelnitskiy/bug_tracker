<?php

namespace Tracker\HomeBundle\Tests\Controller;

use Tracker\TestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'operator',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/');
        self::assertContains('Tracker', $crawler->html());
    }
}
