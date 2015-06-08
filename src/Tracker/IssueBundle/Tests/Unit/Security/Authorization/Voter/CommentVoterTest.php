<?php

namespace Tracker\IssueBundle\Tests\Unit\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Tracker\IssueBundle\Security\Authorization\Voter\CommentVoter;
use Tracker\IssueBundle\Entity\Comment;

class CommentVoterTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommentVoter */
    protected $voter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $tokenInterface;

    protected function setUp()
    {
        $this->voter = new CommentVoter();

        $this->tokenInterface = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }

    public function testSupportsAttributeSuccess()
    {
        $this->assertTrue($this->voter->supportsAttribute(CommentVoter::VIEW));
        $this->assertTrue($this->voter->supportsAttribute(CommentVoter::EDIT));
        $this->assertTrue($this->voter->supportsAttribute(CommentVoter::CREATE));
        $this->assertTrue($this->voter->supportsAttribute(CommentVoter::DELETE));
    }

    public function testSupportsClassSuccess()
    {
        $this->assertTrue($this->voter->supportsClass(get_class(new Comment())));
    }

    public function testVoteSupportsClass()
    {
        $user = $this->getMock('Tracker\UserBundle\Entity\User');
        $user->expects($this->any())->method('getId')->willReturn(1);

        $comment = $this->getMock('Tracker\IssueBundle\Entity\Comment');
        $comment->expects($this->any())->method('getAuthor')->willReturn($user);

        $this->tokenInterface->expects($this->any())->method('getUser')->willReturn($user);

        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote(
                $this->tokenInterface,
                $user,
                array(CommentVoter::EDIT)
            )
        );

        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote(
                $this->tokenInterface,
                $comment,
                array('edit1')
            )
        );

        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->tokenInterface,
                $comment,
                array(CommentVoter::EDIT)
            )
        );

        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->tokenInterface,
                $comment,
                array(CommentVoter::DELETE)
            )
        );
    }

    public function testVoterDenied()
    {
        $user = $this->getMock('Tracker\UserBundle\Entity\User');
        $user->expects($this->any())->method('getId')->willReturn(1);
        $user->expects($this->any())->method('hasRole')->willReturn(false);

        $author = $this->getMock('Tracker\UserBundle\Entity\User');
        $author->expects($this->any())->method('getId')->willReturn(2);


        $comment = $this->getMock('Tracker\IssueBundle\Entity\Comment');
        $comment->expects($this->any())->method('getAuthor')->willReturn($author);

        $this->tokenInterface->expects($this->any())->method('getUser')->willReturn($user);

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->tokenInterface,
                $comment,
                array(CommentVoter::DELETE)
            )
        );

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->tokenInterface,
                $comment,
                array(CommentVoter::EDIT)
            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testVoterCountAttribute()
    {
        $comment = $this->getMock('Tracker\IssueBundle\Entity\Comment');
        $this->tokenInterface->expects($this->any())->method('getUser')->willReturn(null);

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->tokenInterface,
                $comment,
                array(CommentVoter::EDIT, CommentVoter::CREATE)
            )
        );
    }

    public function testVoterEmptyUser()
    {
        $comment = $this->getMock('Tracker\IssueBundle\Entity\Comment');
        $this->tokenInterface->expects($this->any())->method('getUser')->willReturn(null);

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->tokenInterface,
                $comment,
                array(CommentVoter::EDIT)
            )
        );
    }
}
