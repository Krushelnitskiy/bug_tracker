<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 12:11
 */

namespace Tracker\ActivitiesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Issue;
use \Tracker\ProjectBundle\Entity\Project;

/**
 * Class Issue
 * @package Tracker\ActivitiesBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="activity")
 */
class Activity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $event;


    /**
     * @ORM\ManyToOne(targetEntity="\Tracker\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Tracker\IssueBundle\Entity\Issue")
     * @ORM\JoinColumn(name="issue_id", referencedColumnName="id")
     **/
    protected $issue;

    /**
     * @ORM\ManyToOne(targetEntity="\Tracker\ProjectBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     **/
    protected $project;

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
     * Set event
     *
     * @param string $event
     * @return Activity
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param \Tracker\UserBundle\Entity\User $user
     * @return Activity
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Tracker\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set issue
     *
     * @param \Tracker\IssueBundle\Entity\Issue $issue
     * @return Activity
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

    /**
     * Set project
     *
     * @param \Tracker\ProjectBundle\Entity\Project $project
     * @return Activity
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Tracker\ProjectBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }
}
