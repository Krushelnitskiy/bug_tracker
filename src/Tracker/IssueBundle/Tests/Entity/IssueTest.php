<?php

namespace Tracker\IssueBundle\Tests\Entity;

use Tracker\IssueBundle\Entity\Issue;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Issue $issue
     */
    protected $issue;

    protected function setUp()
    {
        $this->issue = new Issue();
    }

    public function testCollaborator()
    {
        $user = $this->getMock("Tracker\\UserBundle\\Entity\\User");


        $this->assertCount(0, $this->issue->getCollaborators());

        $this->issue->addCollaborator($user);
        $this->assertCount(1, $this->issue->getCollaborators());

        $this->issue->removeCollaborator($user);
        $this->assertCount(0, $this->issue->getCollaborators());
    }

    public function testChield()
    {
        $child = new Issue();

        $this->assertCount(0, $this->issue->getChildren());

        $this->issue->addChild($child);
        $this->assertCount(1, $this->issue->getChildren());

        $this->issue->removeChild($child);
        $this->assertCount(0, $this->issue->getChildren());
    }

    public function testComment()
    {
        $comment = $this->getMock("Tracker\\IssueBundle\\Entity\\Comment");

        $this->assertCount(0, $this->issue->getComment());

        $this->issue->addComment($comment);
        $this->assertCount(1, $this->issue->getComment());

        $this->issue->removeComment($comment);
        $this->assertCount(0, $this->issue->getComment());
    }

    public function testParent()
    {
        $issue = new Issue();
        $this->assertEquals(null, $this->issue->getParent());

        $this->issue->setParent($issue);
        $this->assertInstanceOf('Tracker\\IssueBundle\\Entity\\Issue', $this->issue->getParent());

        $this->issue->setParent(null);
        $this->assertEquals(null, $this->issue->getParent());
    }

    public function testToString()
    {
        $this->issue->setSummary('1111');
        $this->issue->setSummary('1111');

        $this->assertEquals('1111', (string)$this->issue);
        $this->assertEquals('1111', $this->issue->getSummary());
    }
}
