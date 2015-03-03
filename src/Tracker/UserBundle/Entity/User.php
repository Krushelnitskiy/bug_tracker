<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 18:18
 */
namespace Tracker\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Tracker\IssueBundle\Entity\Issue;

/**
 * Class Project
 * @package Tracker\UserBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    const ROLE_USER = "ROLE_USER";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_MANAGER = "ROLE_MANAGER";
    const ROLE_OPERATOR = "ROLE_OPERATOR";

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\Tracker\IssueBundle\Entity\Issue", mappedBy="assignee")
     **/
    protected $assignedIssue;

    public function __construct()
    {
        parent::__construct();
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
     * Add assignedIssue
     *
     * @param \Tracker\IssueBundle\Entity\Issue $assignedIssue
     * @return User
     */
    public function addAssignedIssue(\Tracker\IssueBundle\Entity\Issue $assignedIssue)
    {
        $this->assignedIssue[] = $assignedIssue;

        return $this;
    }

    /**
     * Remove assignedIssue
     *
     * @param \Tracker\IssueBundle\Entity\Issue $assignedIssue
     */
    public function removeAssignedIssue(\Tracker\IssueBundle\Entity\Issue $assignedIssue)
    {
        $this->assignedIssue->removeElement($assignedIssue);
    }

    /**
     * Get assignedIssue
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssignedIssue()
    {
        return $this->assignedIssue;
    }
}
