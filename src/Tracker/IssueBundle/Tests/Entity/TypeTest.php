<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11.03.15
 * Time: 14:06
 */

namespace Tracker\IssueBundle\Tests\Entity;

use Tracker\IssueBundle\Entity\Type;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $type Type
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
