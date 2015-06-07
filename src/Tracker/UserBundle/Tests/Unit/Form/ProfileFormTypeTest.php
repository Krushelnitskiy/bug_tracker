<?php

namespace Tracker\UserBundle\Tests\Unit\Form;

use Tracker\UserBundle\Form\ProfileFormType;

class ProfileFormTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProfileFormType */
    protected $type;

    protected function setUp()
    {
        $this->type = new ProfileFormType();
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
        $builder->expects($this->exactly(6))->method('add')->willReturn($builder);
        $this->type->buildForm($builder, []);
    }

    public function testHasName()
    {
        $this->assertEquals('tracker_user_profile', $this->type->getName());
    }

    public function testParent()
    {
        $this->assertEquals('fos_user_profile', $this->type->getParent());
    }
}
