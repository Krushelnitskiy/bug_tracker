<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\UserBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tracker\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

// implements OrderedFixtureInterface

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $password = 'test';

        $userManager = $this->container->get('fos_user.user_manager');
        $encoder_factory = $this->container->get('security.encoder_factory');

        $userAdmin = $userManager->createUser();
        $encoder = $encoder_factory->getEncoder($userAdmin);
        $encodedPass = $encoder->encodePassword($password, $userAdmin->getSalt());

        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('test@test.test');
        $userAdmin->addRole('ROLE_ADMIN');
        $userAdmin->setPassword($encodedPass);
        $userAdmin->setEnabled(1);

        $userManager->updateUser($userAdmin);

        $this->addReference("admin-user", $userAdmin);
    }
}