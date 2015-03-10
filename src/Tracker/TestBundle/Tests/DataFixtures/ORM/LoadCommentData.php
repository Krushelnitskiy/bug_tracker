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
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tracker\IssueBundle\Entity\Comment;
use Tracker\IssueBundle\Entity\Issue;
use Tracker\IssueBundle\Entity\Status;
use Tracker\IssueBundle\Entity\Type;
use Tracker\IssueBundle\Entity\Priority;
use Tracker\ProjectBundle\Entity\Project;
use Tracker\userBundle\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// implements OrderedFixtureInterface

class LoadCommentData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
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
        /**
         * @var $userOperator User
         */
         $userOperator = $this->getReference('user.admin');

         $comment = new Comment();
         $comment->setAuthor($userOperator);
         $comment->setBody('test comment');
         $comment->setCreated(new \DateTime());
         $comment->setIssue($this->getReference('issue.story'));
         $manager->persist($comment);
         $manager->flush();

         $this->addReference('comment.story', $comment);
    }
}
