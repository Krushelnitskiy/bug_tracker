<?php

namespace Tracker\UserBundle\Tests\Unit\Form;

use Tracker\UserBundle\Form\RegistrationFormType;

class RegistrationFormTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var RegistrationFormType */
    protected $type;

    protected function setUp()
    {
        $this->type = new RegistrationFormType();
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

        $builder->expects($this->any())->method('add')->willReturn($builder);
        $builder->expects($this->exactly(6))->method('add');

        $this->type->buildForm($builder, []);
    }

    public function testHasName()
    {
        $this->assertEquals('tracker_user_registration', $this->type->getName());
    }

    public function testParent()
    {
        $this->assertEquals('fos_user_registration', $this->type->getParent());
    }
}
