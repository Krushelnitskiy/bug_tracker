<?php

namespace Tracker\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Tracker\UserBundle\Entity\User;
use Tracker\UserBundle\Entity\Timezone;

class LoadUserData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface
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
        $managerUsers = $this->container->get('fos_user.user_manager');

        $data = $this->getData();

        foreach ($data as $item) {
            /**
             * @var $user User
             */
            $user = $managerUsers->createUser();
            $user->setUsername($item['userName']);
            $user->setEmail($item['email']);
            $user->setFullName($item['fullName']);
            $user->addRole($item['role']);
            $user->setPassword($this->generatePassword($user));
            $user->setTimezone(Timezone::TIMEZONE_EUROPE_KIEV);
            $user->setEnabled(1);

            $managerUsers->updateUser($user);
            $this->addReference($item['referenceKey'], $user);
        }
    }

    /**
     * @param User $user
     * @return string
     */
    protected function generatePassword(User $user)
    {
        $encoderFactory = $this->container->get('security.encoder_factory');
        $password = 'test';

        $encoder = $encoderFactory->getEncoder($user);
        return $encoder->encodePassword($password, $user->getSalt());
    }

    /**
     * @return array
     */
    protected function getData()
    {
        $data = [
            [
                'userName'=> 'admin',
                'email'=>'test@test.test',
                'role'=>User::ROLE_ADMIN,
                'fullName'=>'Administrator',
                'referenceKey'=>'admin-user'
            ],
            [
                'userName'=> 'manager',
                'email'=>'manager@test.test',
                'role'=>User::ROLE_MANAGER,
                'fullName'=>'Manager',
                'referenceKey'=>'user.manager'
            ],
            [
                'userName'=> 'operator',
                'email'=>'operator@test.test',
                'role'=>User::ROLE_OPERATOR,
                'fullName'=>'Operator',
                'referenceKey'=>'user.operator'
            ],
            [
                'userName'=> 'operatorNoProject',
                'email'=>'operator-no-project@test.test',
                'role'=>User::ROLE_OPERATOR,
                'fullName'=>'Operator No Porjects',
                'referenceKey'=>'user.operatorNoProjects'
            ]
        ];

        return $data;
    }
}
