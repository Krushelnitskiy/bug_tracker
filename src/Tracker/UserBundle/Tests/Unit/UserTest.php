<?php

namespace Tracker\UserBundle\Tests\Unit;

use Tracker\UserBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $resolution User
     */
    protected $user;

    protected function setUp()
    {
        $this->user = new User();
    }

    public function testToString()
    {
        $this->user->setFullName('Full Name');
        $this->user->setUsername('User Name');

        $this->assertEquals($this->user->getFullName(), 'Full Name');
        $this->assertEquals($this->user->getUsername(), 'User Name');
        $this->assertEquals($this->user->getUsername(), (string)$this->user);
    }

    public function testProject()
    {
        $this->assertCount(0, $this->user->getProject());

        $project = $this->getMock("Tracker\\ProjectBundle\\Entity\\Project");
        $this->user->addProject($project);
        $this->assertCount(1, $this->user->getProject());

        $this->user->removeProject($project);
        $this->assertCount(0, $this->user->getProject());
    }

    public function testAssignedIssue()
    {
        $this->assertCount(0, $this->user->getAssignedIssue());

        $issue = $this->getMock("Tracker\\IssueBundle\\Entity\\Issue");
        $this->user->addAssignedIssue($issue);
        $this->assertCount(1, $this->user->getAssignedIssue());

        $this->user->removeAssignedIssue($issue);
        $this->assertCount(0, $this->user->getAssignedIssue());
    }

    public function testPath()
    {
        $this->user->setPath(('\test\path\dir'));

        $this->assertEquals($this->user->getPath(), '\test\path\dir');
    }
}
