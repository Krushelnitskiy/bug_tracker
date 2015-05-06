<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 11:44
 */

namespace Tracker\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Issue;

/**
 * Class Comment
 * @package Tracker\IssueBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="issue_comment")
 */
class Comment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Tracker\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     **/
    protected $author;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $body;

    /**
     * @ORM\Column(type="date")
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="comment")
     * @ORM\JoinColumn(name="issue_id", referencedColumnName="id")
     **/
    protected $issue;

    /**
     * Set body
     *
     * @param string $body
     * @return Comment
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Comment
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set author
     *
     * @param \Tracker\UserBundle\Entity\User $author
     * @return Comment
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Tracker\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set issue
     *
     * @param \Tracker\IssueBundle\Entity\Issue $issue
     * @return Comment
     */
    public function setIssue(Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return \Tracker\IssueBundle\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }
}
