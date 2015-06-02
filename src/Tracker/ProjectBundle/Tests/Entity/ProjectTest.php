<?php

namespace Tracker\ProjectBundle\Tests\Entity;

use Tracker\ProjectBundle\Entity\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $project Project
     */
    protected $project;

    protected function setUp()
    {
        $this->project = new Project();
    }

    public function testToString()
    {
        $this->project->setLabel('111');

        $this->assertEquals($this->project->getLabel(), (string)$this->project);
    }

    public function testMembers()
    {
        $user = $this->getMockBuilder('Tracker\UserBundle\Entity\User')
                    ->setMethods(array('getId', 'getLabel'))
                    ->getMock();

        $user->method('getId')->willReturn('1');
        $user->method('getLabel')->willReturn('project 1');

        $this->assertCount(0, $this->project->getMembers());

        $this->project->addMember($user);
        $this->assertCount(1, $this->project->getMembers());

        $this->project->removeMember($user);
        $this->assertCount(0, $this->project->getMembers());
    }
}
