<?php

namespace Tracker\ActivitiesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Issue;
use Tracker\ProjectBundle\Entity\Project;
use Tracker\ActivitiesBundle\Entity;

/**
 * Class Issue
 * @package Tracker\ActivitiesBundle\Entity
 * @ORM\Entity(repositoryClass="Tracker\ActivitiesBundle\Entity\Repository\ActivityRepository")
 * @ORM\Table(name="activity")
 */
class Activity
{
    const CREATE_NEW_ISSUE = 'activity.event.create_new_issue';
    const CHANGED_STATUS_TO = 'activity.event.changed_status_to';
    const CREATED_COMMENT = 'activity.event.created_comment';

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
     * @ORM\Column(type="datetime")
     **/
    protected $created;

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
     * @param User $user
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set issue
     *
     * @param Issue $issue
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
     * @return Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * Set project
     *
     * @param Project $project
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
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Issue
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
}
