<?php

namespace Tracker\IssueBundle\Tests\Entity;

use Tracker\IssueBundle\Entity\Type;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Type $type
     */
    protected $type;

    protected function setUp()
    {
        $this->type = new Type();
    }

    public function testToString()
    {
        $this->type->setValue('111');

        $this->assertEquals($this->type->getValue(), (string)$this->type);
    }
}
