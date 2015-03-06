<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 17:32
 */

namespace Tracker\TestBundle\Tests\DataFixtures\ORM;

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
        $userAdmin = $userManager->createUser();
        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('test@test.test');
        $userAdmin->setPlainPassword($password);
        $userAdmin->setEnabled(true);
        $userAdmin->addRole(User::ROLE_ADMIN);
        $userManager->updateUser($userAdmin);

        $userRoleManager = $userManager->createUser();
        $userRoleManager->setUsername('manager');
        $userRoleManager->setEmail('manager.test@test.test');
        $userRoleManager->setPlainPassword($password);
        $userRoleManager->setEnabled(true);
        $userRoleManager->addRole(User::ROLE_MANAGER);
        $userManager->updateUser($userRoleManager);

        $userOperator = $userManager->createUser();
        $userOperator->setUsername('operator');
        $userOperator->setEmail('operator.test@test.test');
        $userOperator->setPlainPassword($password);
        $userOperator->setEnabled(true);
        $userOperator->addRole(User::ROLE_OPERATOR);
        $userManager->updateUser($userOperator);

        $userOperatorNoProject = $userManager->createUser();
        $userOperatorNoProject->setUsername('operator.noProjects');
        $userOperatorNoProject->setEmail('operator.noProjects.test@test.test');
        $userOperatorNoProject->setPlainPassword($password);
        $userOperatorNoProject->setEnabled(true);
        $userOperatorNoProject->addRole(User::ROLE_OPERATOR);
        $userManager->updateUser($userOperatorNoProject);

        $user = $userManager->createUser();
        $user->setUsername('user');
        $user->setEmail('user.test@test.test');
        $user->setPlainPassword($password);
        $user->setEnabled(true);
        $user->addRole(User::ROLE_USER);
        $userManager->updateUser($user);

        $this->addReference('user.admin', $userAdmin);
        $this->addReference('user.manager', $userRoleManager);
        $this->addReference('user.operator', $userOperator);
        $this->addReference('user.operator.noProjects', $userOperatorNoProject);
        $this->addReference('user.user', $user);
    }
}
