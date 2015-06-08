<?php

namespace Tracker\UserBundle\Tests\Unit\Form;

use Symfony\Component\Translation\TranslatorInterface;

use Tracker\UserBundle\Form\UserType;

class UserTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var UserType */
    protected $type;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function testFields()
    {
        $user = $this->getMock('Tracker\UserBundle\Entity\User');
        $user->expects($this->exactly(2))->method('getId')->willReturn(false);

        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $builder->expects($this->exactly(9))->method('add')->willReturn($builder);
        $builder->expects($this->exactly(2))->method('getData')->willReturn($user);
        $builder->expects($this->exactly(4))->method('create')->willReturn($builder);

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
        $this->assertEquals('tracker_userBundle_user', $this->type->getName());
    }

    protected function setUp()
    {
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->type = new UserType($this->translator);
    }

    protected function tearDown()
    {
        unset($this->type);
    }
}
