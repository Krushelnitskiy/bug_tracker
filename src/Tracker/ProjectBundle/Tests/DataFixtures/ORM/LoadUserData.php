<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\ProjectBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tracker\UserBundle\Entity\User;

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

//        $encoderFactory = $this->container->get('security.encoder_factory');

        $userAdmin = $userManager->createUser();

//        $encoder = $encoderFactory->getEncoder($userAdmin);
//        $encodedPass = $encoder->encodePassword($password, $userAdmin->getSalt());

        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('test@test.test');
        $userAdmin->setPlainPassword($password);
        $userAdmin->setEnabled(true);
        $userAdmin->addRole(User::ROLE_ADMIN);

        $userManager->updateUser($userAdmin);

        $this->addReference("admin-user", $userAdmin);
    }
}
