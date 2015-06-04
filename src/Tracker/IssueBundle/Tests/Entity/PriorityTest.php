<?php

namespace Tracker\IssueBundle\Tests\Entity;

use Tracker\IssueBundle\Entity\Priority;

class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Priority $issue
     */
    protected $priority;

    protected function setUp()
    {
        $this->priority = new Priority();
    }

    public function testToString()
    {
        $this->priority->setValue('1111');

        $this->assertEquals('1111', (string)$this->priority);
        $this->assertEquals('1111', $this->priority->getValue());
    }
}
