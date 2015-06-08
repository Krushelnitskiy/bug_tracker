<?php

namespace Tracker\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Validator\Constraints as Assert;

use Tracker\UserBundle\Entity\User;

/**
 * Class Project
 *
 * @package Tracker\ProjectBundle\Entity
 * @ORM\Entity(repositoryClass="Tracker\ProjectBundle\Entity\ProjectRepository")
 * @ORM\Table(name="project")
 */
class Project
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $label
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $label;

    /**
     * @var string $summary
     *
     * @ORM\Column(type="text")
     */
    protected $summary;

    /**
     * @var string $code
     *
     * @ORM\Column(type="string", length=50, unique=true, nullable=true)
     */
    protected $code;

    /**
     * @var User[] $code
     * @Assert\NotBlank()
     * @ORM\ManyToMany(targetEntity="\Tracker\UserBundle\Entity\User", inversedBy="project")
     * @ORM\JoinTable(name="projects_users")
     **/
    protected $members;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(type="datetime")
     **/
    protected $created;

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
     *
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
     *
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
     *
     * @return Project
     */
    public function setCode($code)
    {
        $this->code = strtoupper($code);

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
     * @param User $members
     *
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
     * @param User $members
     */
    public function removeMember(User $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->label;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Project
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
