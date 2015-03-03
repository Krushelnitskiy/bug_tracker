<?php

namespace Tracker\UserBundle\Tests\Controller;

use Tracker\TestBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testViewList()
    {

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $crawler = $client->request('GET', '/user');
        $crawler = $client->followRedirect();
        $this->assertContains('Create new user', $crawler->html());
    }

    public function testCreate()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $crawler = $client->request('GET', '/user/new');

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_userbundle_user[email]'] = 'test1@test.com';
        $form['tracker_userbundle_user[username]']='test';
        $form['tracker_userbundle_user[plainPassword][first]']= '123';
        $form['tracker_userbundle_user[plainPassword][second]']= '123';
        $form['tracker_userbundle_user[roles]']= array('ROLE_ADMIN', "ROLE_MANAGER");
        $form['tracker_userbundle_user[enabled]']= 1;
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertContains('test1@test.com', $crawler->html());
    }

    public function testShow()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $crawler = $client->request('GET', '/user/'.$this->getReference('admin-user')->getId());
        $this->assertContains($this->getReference('admin-user')->getEmail(), $crawler->html());
    }

    public function testEdit()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));

        $userId = $this->getReference('admin-user')->getId();
        $crawler = $client->request('GET', '/user/'.$userId.'/edit');

        $form = $crawler->selectButton('Update')->form();
        $form['tracker_userbundle_user[email]'] = 'test_admin@test.com';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertContains('test_admin@test.com', $crawler->html());
    }
}
