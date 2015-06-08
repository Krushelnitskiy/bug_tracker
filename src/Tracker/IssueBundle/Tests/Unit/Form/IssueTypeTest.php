<?php

namespace Tracker\IssueBundle\Tests\Unit\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Tracker\IssueBundle\Form\IssueType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var IssueType */
    protected $type;

    /** @var  TokenStorageInterface */
    protected $tokenStorage;

    protected function setUp()
    {
        $namespace = 'Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface';
        $this->tokenStorage = $this->getMockBuilder($namespace)
            ->disableOriginalConstructor()
            ->getMock();

        $this->type = new IssueType($this->tokenStorage);
    }

    protected function tearDown()
    {
        unset($this->type);
    }

    public function testFields()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();


        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);
        $ownerClass = 'Tracker\UserBundle\Entity\User';
        $owner      = $this->getMock($ownerClass);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($owner);


        $issue = $this->getMockBuilder('Tracker\IssueBundle\Entity\issue')
            ->disableOriginalConstructor()
            ->getMock();
        $issue->expects($this->any())->method('getId')->willReturn(null);

        $builder->expects($this->any())->method('add')->willReturn($builder);
        $builder->expects($this->exactly(5))->method('getData')
            ->willReturn($issue);

        $project = $this->getMock('Tracker\ProjectBundle\Entity\Project');
        $project->expects($this->any())->method('getId')->willReturn(1);
        $project->expects($this->any())->method('getMembers')->willReturn(new ArrayCollection());

        $options =[
            'projects'=> [$project]
        ];

        $this->type->buildForm($builder, $options);
    }

    public function testSetDefaultOptions()
    {
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));
        $this->type->setDefaultOptions($resolver);
    }

    public function testHasName()
    {
        $this->assertEquals('tracker_issueBundle_issue', $this->type->getName());
    }
}
