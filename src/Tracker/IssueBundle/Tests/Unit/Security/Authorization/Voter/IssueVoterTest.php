<?php

namespace Tracker\IssueBundle\Tests\Unit\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Tracker\IssueBundle\Security\Authorization\Voter\IssueVoter;
use Tracker\IssueBundle\Entity\Comment;
use Tracker\IssueBundle\Entity\Issue;
use Tracker\UserBundle\Entity\User;

class IssueVoterTest extends \PHPUnit_Framework_TestCase
{
    /** @var IssueVoter */
    protected $voter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $tokenInterface;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockUser;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockEntity;

    protected function setUp()
    {
        $this->voter = new IssueVoter();

        $this->tokenInterface = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->mockUserEntity = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockObject = $this->getMockBuilder('Oro\Bundle\TrackerBundle\Entity\Comment')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testSupportsAttributeSuccess()
    {
        $this->assertTrue($this->voter->supportsAttribute(IssueVoter::VIEW));
        $this->assertTrue($this->voter->supportsAttribute(IssueVoter::EDIT));
        $this->assertTrue($this->voter->supportsAttribute(IssueVoter::CREATE));
        $this->assertTrue($this->voter->supportsAttribute(IssueVoter::CREATE_SUB_TASK));
    }

    public function testSupportsClassSuccess()
    {
        $this->assertTrue($this->voter->supportsClass(get_class(new Issue())));
    }

    public function testVoteSupportClass()
    {
        $comment = $this->getMock('Tracker\IssueBundle\Entity\Comment');

        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote(
                $this->tokenInterface,
                $comment,
                array(IssueVoter::EDIT)
            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testVoteCountAttribute()
    {
        $issue = $this->getMock('Tracker\IssueBundle\Entity\Issue');

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->tokenInterface,
                $issue,
                array(IssueVoter::EDIT, IssueVoter::CREATE)
            )
        );
    }

    public function testVoteEmptyUser()
    {
        $issue = $this->getMock('Tracker\IssueBundle\Entity\Issue');
        $this->tokenInterface->expects($this->any())->method('getUser')->willReturn(null);

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->tokenInterface,
                $issue,
                array(IssueVoter::EDIT)
            )
        );
    }


    public function testVoteSupportAttribute()
    {
        $issue = $this->getMock('Tracker\IssueBundle\Entity\Issue');

        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote(
                $this->tokenInterface,
                $issue,
                array('edit1')
            )
        );
    }
}
