<?php

namespace Tracker\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Status
 * @package Tracker\IssueBundle\Entity
 * @ORM\Table(name="issue_status")
 * @ORM\Entity(repositoryClass="Tracker\IssueBundle\Entity\StatusRepository")
 */
class Status
{
    const STATUS_OPEN = 'Open';
    const STATUS_IN_PROGRESS = 'In progress';
    const STATUS_CLOSED = 'Closed';

    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="string", unique=true, nullable=false)
     */
    protected $value;

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
     * Set value
     *
     * @param string $value
     *
     * @return Status
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
