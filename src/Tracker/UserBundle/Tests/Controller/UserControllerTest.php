<?php

namespace Tracker\UserBundle\Tests\Controller;

use Tracker\TestBundle\Test\WebTestCase;
use Tracker\UserBundle\Entity\Timezone;
use Tracker\UserBundle\Entity\User;

class UserControllerTest extends WebTestCase
{
    public function testViewList()
    {

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $client->request('GET', '/user');
        $crawler = $client->followRedirect();
        $this->assertContains('Create new user', $crawler->html());
    }

    public function testCreate()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/user/new');

        $form = $crawler->selectButton('Create')->form();
        $form['tracker_userBundle_user[email]'] = 'test1@test.com';
        $form['tracker_userBundle_user[username]']='test';
        $form['tracker_userBundle_user[plainPassword][first]']= '123';
        $form['tracker_userBundle_user[plainPassword][second]']= '123';
        $form['tracker_userBundle_user[roles]']= User::ROLE_MANAGER;
        $form['tracker_userBundle_user[enabled]']= 1;
        $form['tracker_userBundle_user[timezone]']= Timezone::TIMEZONE_EUROPE_KIEV;

        $client->followRedirects();
        $client->submit($form);
        $crawler = $client->getCrawler();

        $this->assertContains('test1@test.com', $crawler->html());
    }

    public function testShow()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $crawler = $client->request('GET', '/user/'.$this->getReference('user.admin')->getId());
        $this->assertContains($this->getReference('user.admin')->getEmail(), $crawler->html());
    }

    public function testEdit()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test'
        ));

        $userId = $this->getReference('user.admin')->getId();
        $crawler = $client->request('GET', '/user/'.$userId.'/edit');

        $form = $crawler->selectButton('Update')->form();
        $form['tracker_userBundle_user[email]'] = 'test_admin111@test.com';
        $client->followRedirects();
        $client->submit($form);
        $crawler = $client->getCrawler();

        $this->assertContains('test_admin111@test.com', $crawler->html());
    }
}
