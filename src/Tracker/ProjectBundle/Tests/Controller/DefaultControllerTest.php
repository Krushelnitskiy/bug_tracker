<?php

namespace Tracker\ProjectBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
//    public function testIndex()
//    {
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', '/hello/Fabien');
//
//        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
//    }

    public function testCreate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/project/new');

        $form = $crawler->selectButton('Create')
            ->form(array(
            'tracker_projectbundle_project[label]'  => 'label',
            'tracker_projectbundle_project[summary]'  => 'summary',
            'tracker_projectbundle_project[code]'  => 'code'))
        ;

        $form['tracker_projectbundle_project[members]']->select(array('25'));

        $crawler = $client->submit($form);

        $this->assertContains('label', $crawler->html());
    }

//    public function testIndex()
//    {
        // Create a new client to browse the application
//        $client = static::createClient();
//
//         Create a new entry in the database
//        $crawler = $client->request('GET', '/project/');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /project/");
//
//        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
//         Fill in the form and submit it
//        $form = $crawler->selectButton('Create')->form(array(
//            'tracker_projectbundle_project[label]'  => 'label',
//            'tracker_projectbundle_project[summary]'  => 'summary',
//            'tracker_projectbundle_project[code]'  => 'code'
//             ... other fields to fill
//        ));
//        $form['tracker_projectbundle_project[members]']->select(array('25'));
//
//
//        $crawler = $client->followRedirect();
//        $client->submit($form);
//
//
//
        // Check data in the show view
//        $this->assertGreaterThan(0, $crawler->filter('td:contains("label")')->count(), 'Missing element td:contains("label")');

//        // Edit the entity
//        $crawler = $client->click($crawler->selectLink('Edit')->link());
//
//        $form = $crawler->selectButton('Update')->form(array(
//            'tracker_projectbundle_project[label]'  => 'Foo',
//             ... other fields to fill
//        ));

//        $client->submit($form);
//        $crawler = $client->followRedirect();
//
//         Check the element contains an attribute with value equals "Foo"
//        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');
//
//        // Delete the entity
//        $client->submit($crawler->selectButton('Delete')->form());
//        $crawler = $client->followRedirect();
//
//        // Check the entity has been delete on the list
//        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
//    }
}
