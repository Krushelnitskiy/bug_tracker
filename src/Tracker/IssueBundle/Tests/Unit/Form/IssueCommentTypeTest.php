<?php

namespace Tracker\IssueBundle\Tests\Unit\Form;

use Tracker\IssueBundle\Form\IssueCommentType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IssueCommentTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var IssueCommentType */
    protected $type;

    /** @var  TokenStorageInterface */
    protected $tokenStorage;

    protected function setUp()
    {
        $namespace = 'Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface';
        $this->tokenStorage = $this->getMockBuilder($namespace)
            ->disableOriginalConstructor()
            ->getMock();

        $this->type = new IssueCommentType($this->tokenStorage);
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

        $comment = $this->getMockBuilder('Tracker\IssueBundle\Entity\Comment')
            ->disableOriginalConstructor()
            ->getMock();
        $comment->expects($this->any())->method('getId')->willReturn(null);

        $builder->expects($this->any())->method('add')->willReturn($builder);
        $builder->expects($this->exactly(1))->method('getData')
            ->willReturn($comment);

        $builder->expects($this->exactly(2))->method('add');

        $this->type->buildForm($builder, []);
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
        $this->assertEquals('tracker_issueBundle_comment_form', $this->type->getName());
    }
}
