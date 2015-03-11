<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11.03.15
 * Time: 14:06
 */

namespace Tracker\IssueBundle\Tests\Entity;

use Tracker\IssueBundle\Entity\Resolution;

class ResolutionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $resolution Resolution
     */
    protected $resolution;

    protected function setUp()
    {
        $this->resolution = new Resolution();
    }

    public function testToString()
    {
        $this->resolution->setValue('111');

        $this->assertEquals($this->resolution->getValue(), '111');
        $this->assertEquals($this->resolution->getValue(), (string)$this->resolution);
    }
}
