<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 18:22
 */

namespace Tracker\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tracker\UserBundle\Entity\User;

/**
 * Class Project
 * @package Tracker\ProjectBundle\Entity
 * @ORM\Entity(repositoryClass="Tracker\ProjectBundle\Entity\ProjectRepository")
 * @ORM\Table(name="project")
 * @UniqueEntity("code")
 */
class Project
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $label;

    /**
     * @ORM\Column(type="text")
     */
    protected $summary;

    /**
     * @Assert\Regex(
     *      pattern="/^[\w\-]+$/",
     *      match = true,
     *      message="project.error.invalidCodeValue"
     * )
     * @ORM\Column(type="string", length=100, unique=true)
     */
    protected $code;

    /**
     * @ORM\ManyToMany(targetEntity="\Tracker\UserBundle\Entity\User", inversedBy="project")
     * @ORM\JoinTable(name="projects_users")
     **/
    protected $members;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
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
     * Set label
     *
     * @param string $label
     * @return Project
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Project
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Project
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add members
     *
     * @param \Tracker\UserBundle\Entity\User $members
     * @return Project
     */
    public function addMember(User $members)
    {
        $this->members[] = $members;

        return $this;
    }

    /**
     * Remove members
     *
     * @param \Tracker\UserBundle\Entity\User $members
     */
    public function removeMember(User $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    public function __toString()
    {
        return $this->label;
    }
}
